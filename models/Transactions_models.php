<?php
class Transactions_models {
   private $conn;
    private $table_name = "Tbl_Transaksi";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllTransactions() {
        $query = "SELECT t.*, u.nama_user FROM " . $this->table_name . " t JOIN Tbl_User u ON t.id_user = u.id_user ORDER BY t.tgl_transaksi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTransaction($data) {
        $query = "INSERT INTO " . $this->table_name . " SET id_user=:id_user, tgl_transaksi=:tgl_transaksi, nilai_transaksi=:nilai_transaksi, ket_transaksi=:ket_transaksi, status=:status";
        
        // Menggunakan prepare dan execute untuk keamanan
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(":id_user", $data['id_user']);
        $stmt->bindParam(":tgl_transaksi", $data['tgl_transaksi']);
        $stmt->bindParam(":nilai_transaksi", $data['nilai_transaksi']);
        $stmt->bindParam(":ket_transaksi", $data['ket_transaksi']);
        $stmt->bindParam(":status", $data['status']);

        if ($stmt->execute()) {
            return $stmt->rowCount();
        }
        return 0;
    }

    public function getDashboardData() {
        $queryPemasukan = "SELECT SUM(nilai_transaksi) as total FROM " . $this->table_name . " WHERE status = 'pm' AND MONTH(tgl_transaksi) = MONTH(CURRENT_DATE()) AND YEAR(tgl_transaksi) = YEAR(CURRENT_DATE())";
        $stmtPemasukan = $this->conn->prepare($queryPemasukan);
        $stmtPemasukan->execute();
        $pemasukan = $stmtPemasukan->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $queryPengeluaran = "SELECT SUM(nilai_transaksi) as total FROM " . $this->table_name . " WHERE status = 'pg' AND MONTH(tgl_transaksi) = MONTH(CURRENT_DATE()) AND YEAR(tgl_transaksi) = YEAR(CURRENT_DATE())";
        $stmtPengeluaran = $this->conn->prepare($queryPengeluaran);
        $stmtPengeluaran->execute();
        $pengeluaran = $stmtPengeluaran->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        return [
            'pemasukan_bulan_ini' => (float) $pemasukan,
            'pengeluaran_bulan_ini' => (float) $pengeluaran,
            'saldo_bulan_ini' => (float) ($pemasukan - $pengeluaran)
        ];
    }
    
    public function getReportData($startDate, $endDate) {
        $query = "SELECT t.*, u.nama_user FROM " . $this->table_name . " t JOIN Tbl_User u ON t.id_user = u.id_user WHERE t.tgl_transaksi BETWEEN :start_date AND :end_date ORDER BY t.tgl_transaksi ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}