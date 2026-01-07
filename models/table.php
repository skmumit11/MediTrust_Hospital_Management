<?php
require_once('db.php');

/* ---------------- GET ALL ---------------- */
function getAll<TableName>(){
    $con = getConnection();
    $sql = "SELECT * FROM <TableName>";
    $result = mysqli_query($con, $sql);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

/* ---------------- GET BY ID ---------------- */
function get<TableName>ById($id){
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT * FROM <TableName> WHERE <PrimaryKey>=$id";
    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

/* ---------------- ADD ---------------- */
function add<TableName>($data){
    $con = getConnection();
    // Build INSERT query manually for your table fields
    $sql = "INSERT INTO <TableName> (...) VALUES (...)";
    return mysqli_query($con, $sql);
}

/* ---------------- UPDATE ---------------- */
function update<TableName>($data){
    $con = getConnection();
    $id = (int)$data['<PrimaryKey>'];
    // Build UPDATE query manually for your table fields
    $sql = "UPDATE <TableName> SET ... WHERE <PrimaryKey>=$id";
    return mysqli_query($con, $sql);
}

/* ---------------- DELETE ---------------- */
function delete<TableName>($id){
    $con = getConnection();
    $id = (int)$id;
    $sql = "DELETE FROM <TableName> WHERE <PrimaryKey>=$id";
    return mysqli_query($con, $sql);
}
?>
