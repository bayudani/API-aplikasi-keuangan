<?php
class Transaction_model {
    private $table = 'Tbl_Transaksi';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTransactions() {
        // Join dengan tabel user untuk mendapatkan nama user
        $this->db->query('SELECT Tbl_Transaksi.*, Tbl_User.nama_user FROM ' . $this->table . ' JOIN Tbl_User ON Tbl_Transaksi.id_user = Tbl_User.id_user ORDER BY tgl_transaksi DESC');
        return $this->db->resultSet();
    }

    public function createTransaction($data) {
        $query = "INSERT INTO Tbl_Transaksi (id_user, tgl_transaksi, nilai_transaksi, ket_transaksi, status) VALUES (:id_user, :tgl_transaksi, :nilai_transaksi, :ket_transaksi, :status)";
        
        $this->db->query($query);
        $this->db->bind('id_user', $data['id_user']);
        $this->db->bind('tgl_transaksi', $data['tgl_transaksi']);
        $this->db->bind('nilai_transaksi', $data['nilai_transaksi']);
        $this->db->bind('ket_transaksi', $data['ket_transaksi']);
        $this->db->bind('status', $data['status']);

        $this->db->execute();
        return $this->db->rowCount();
    }
    
    public function getDashboardData() {
        // Contoh query untuk data dashboard: total pemasukan dan pengeluaran bulan ini
        $queryPemasukan = "SELECT SUM(nilai_transaksi) as total FROM Tbl_Transaksi WHERE status = 'pm' AND MONTH(tgl_transaksi) = MONTH(CURRENT_DATE()) AND YEAR(tgl_transaksi) = YEAR(CURRENT_DATE())";
        $this->db->query($queryPemasukan);
        $pemasukan = $this->db->single()['total'] ?? 0;

        $queryPengeluaran = "SELECT SUM(nilai_transaksi) as total FROM Tbl_Transaksi WHERE status = 'pg' AND MONTH(tgl_transaksi) = MONTH(CURRENT_DATE()) AND YEAR(tgl_transaksi) = YEAR(CURRENT_DATE())";
        $this->db->query($queryPengeluaran);
        $pengeluaran = $this->db->single()['total'] ?? 0;
        
        return [
            'pemasukan_bulan_ini' => $pemasukan,
            'pengeluaran_bulan_ini' => $pengeluaran,
            'saldo_bulan_ini' => $pemasukan - $pengeluaran
        ];
    }
    
    public function getReportData($startDate, $endDate) {
        $query = 'SELECT Tbl_Transaksi.*, Tbl_User.nama_user FROM ' . $this->table . ' JOIN Tbl_User ON Tbl_Transaksi.id_user = Tbl_User.id_user WHERE tgl_transaksi BETWEEN :start_date AND :end_date ORDER BY tgl_transaksi ASC';
        $this->db->query($query);
        $this->db->bind('start_date', $startDate);
        $this->db->bind('end_date', $endDate);
        return $this->db->resultSet();
    }
}