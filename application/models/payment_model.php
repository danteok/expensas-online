<?php
class Payment_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database(); // Carga la base de datos en el constructor
    }

    public function checkIfExists($subscription_id, $period) {
        
        $query = $this->db->get_where('payments', array('subscription_id' => $subscription_id,'period'=>$period.'-01'));
        $result =  $query->row_array();
        if($result){
            return true;
        }
        return false;
    }

    public function insert($data) {
        // Inserta los datos del usuario en la base de datos
        $this->db->insert('payments', $data);
        // Retorna el ID del último registro insertado
        return $this->db->insert_id();
    }

    public function get_all_payments($period) {
        $query = $this->db->get_where('payments', array('period' => $period));
        return $query->result();
    }

    public function get_resume_payments($period) {
        $query = $this->db->select(" SUM(amount) total_amount, count(id) qantity_payments " )->get_where('payments', array('period' => $period));


        return $query->result();
    }

}