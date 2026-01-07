
<?php
// controllers/subscriptionsController.php
if (!isset($_SESSION)) { session_start(); }

require_once __DIR__ . '/../models/subscriptionModel.php';
require_once __DIR__ . '/../models/db.php';

$message = "";
$messageType = "success";
$activeSubscription = null;
$plans = sub_getSubscriptionPlans();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$userId   = $username !== '' ? sub_getUserIdByUsername($username) : null;
$patientId = null;

// Resolve patient for logged-in user, else allow guest flow with self-entry
if ($userId !== null) {
    $patientId = sub_getPatientIdForUser($userId);
    // If the user has no linked Patient, we can lazily create one with minimal info
    if ($patientId === null) {
        $conn = getConnection();
        $name = isset($_SESSION['name']) ? $_SESSION['name'] : $username;
        $gender = 'Other'; // enum without regex
        $contact = ''; $address = '';
        $stmt = $conn->prepare("INSERT INTO Patient (UserID, Name, Age, Gender, Contact, Address, PatientCategory, CreatedAt, Notes)
                                VALUES (?, ?, NULL, ?, ?, ?, 'Unknown', NOW(), NULL)");
        $stmt->bind_param('issss', $userId, $name, $gender, $contact, $address);
        $stmt->execute();
        $patientId = $stmt->insert_id;
        $stmt->close();
        closeConnection($conn);
    }
}

// Load current active subscription (if any) for the patient
if ($patientId !== null) {
    $activeSubscription = sub_getActiveSubscription($patientId);
}

// Handle POST: select/pay or cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'pay_subscription') {
        if ($patientId === null) {
            $message = "Unable to resolve patient. Please login first.";
            $messageType = "error";
        } else {
            $planId = isset($_POST['PlanID']) ? (int)$_POST['PlanID'] : 0;
            $cardHolder = isset($_POST['CardHolder']) ? trim($_POST['CardHolder']) : '';
            $cardNumber = isset($_POST['CardNumber']) ? trim($_POST['CardNumber']) : '';
            $cardExpiry = isset($_POST['CardExpiry']) ? trim($_POST['CardExpiry']) : '';
            $cvv        = isset($_POST['CVV']) ? trim($_POST['CVV']) : '';

            if ($planId <= 0 || $cardHolder === '' || $cardNumber === '' || $cardExpiry === '' || $cvv === '') {
                $message = "Please fill all required fields.";
                $messageType = "error";
            } else {
                if ($activeSubscription !== null) {
                    $message = "You already have an active subscription. Cancel it first to switch packages.";
                    $messageType = "error";
                } else {
                    // Simple card type detection without regex
                    $first = substr($cardNumber, 0, 1);
                    $cardType = 'Other';
                    if ($first === '4') { $cardType = 'Visa'; }
                    else if ($first === '5') { $cardType = 'MasterCard'; }

                    $pmId = sub_createPaymentMethod($patientId, $cardHolder, $cardNumber, $cardExpiry, $cardType, true);

                    $plan = sub_getPlanById($planId);
                    if (!$plan) {
                        $message = "Invalid plan selected.";
                        $messageType = "error";
                    } else {
                        $paidAt = date('Y-m-d H:i:s');
                        // ExpiryDate = PaidAt + Duration days
                        $expiryDate = date('Y-m-d H:i:s', strtotime($paidAt . ' +' . (int)$plan['Duration'] . ' days'));

                        $subId = sub_createPatientSubscription($patientId, $planId, $paidAt, $expiryDate);

                        // Simulate successful payment entry
                        $txnRef = 'TXN-' . bin2hex(random_bytes(6));
                        sub_addSubscriptionPayment($subId, $patientId, (float)$plan['Price'], $cardType, 'Success', $txnRef);

                        $message = "Subscription activated: " . $plan['PlanName'] . ". TxnRef: " . $txnRef;
                        $messageType = "success";

                        $activeSubscription = sub_getActiveSubscription($patientId);
                    }
                }
            }
        }
    }
    else if ($action === 'cancel_subscription') {
        $subId = isset($_POST['SubscriptionID']) ? (int)$_POST['SubscriptionID'] : 0;
        if ($subId <= 0) {
            $message = "Invalid subscription.";
            $messageType = "error";
        } else {
            $ok = sub_cancelActiveSubscription($subId);
            if ($ok) {
                $message = "Subscription cancelled.";
                $messageType = "success";
                $activeSubscription = null;
            } else {
                $message = "Failed to cancel subscription.";
                $messageType = "error";
            }
        }
    }
}

// Expose data to view
$plansList = $plans;
$currentActive = $activeSubscription;
