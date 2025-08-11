<?php
class Transactions_models {
    private $conn;
    private $table_name = "Tbl_Transaksi";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllTransactions() {
        $query = "SELECT t.*, u.nama_user FROM " . $this->table_name . " t JOIN Tbl_User u ON t.id_user = u.id_user ORDER BY t.tgl_transaksi DESC, t.no_transaksi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BARU: Fungsi untuk mengambil transaksi berdasarkan ID user (untuk Karyawan)
    public function getTransactionsByUserId($id_user) {
        $query = "SELECT t.*, u.nama_user FROM " . $this->table_name . " t JOIN Tbl_User u ON t.id_user = u.id_user WHERE t.id_user = :id_user ORDER BY t.tgl_transaksi DESC, t.no_transaksi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTransaction($data) {
        $query = "INSERT INTO " . $this->table_name . " SET id_user=:id_user, tgl_transaksi=:tgl_transaksi, nilai_transaksi=:nilai_transaksi, ket_transaksi=:ket_transaksi, status=:status";
        $stmt = $this->conn->prepare($query);
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

    // BARU: Fungsi untuk mengupdate transaksi
    public function updateTransaction($id, $data) {
        // Query ini hanya akan mengupdate field yang dikirim saja
        $fields = [];
        if (isset($data['tgl_transaksi'])) $fields[] = "tgl_transaksi = :tgl_transaksi";
        if (isset($data['nilai_transaksi'])) $fields[] = "nilai_transaksi = :nilai_transaksi";
        if (isset($data['ket_transaksi'])) $fields[] = "ket_transaksi = :ket_transaksi";
        if (isset($data['status'])) $fields[] = "status = :status";
        if (empty($fields)) return 0; // Tidak ada yang diupdate

        $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $fields) . " WHERE no_transaksi = :id";
        $stmt = $this->conn->prepare($query);

        // Bind data yang ada
        if (isset($data['tgl_transaksi'])) $stmt->bindParam(":tgl_transaksi", $data['tgl_transaksi']);
        if (isset($data['nilai_transaksi'])) $stmt->bindParam(":nilai_transaksi", $data['nilai_transaksi']);
        if (isset($data['ket_transaksi'])) $stmt->bindParam(":ket_transaksi", $data['ket_transaksi']);
        if (isset($data['status'])) $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return $stmt->rowCount();
        }
        return 0;
    }
    
    // BARU: Fungsi untuk menghapus transaksi
    public function deleteTransaction($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE no_transaksi = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
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