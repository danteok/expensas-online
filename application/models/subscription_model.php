<?php
class Subscription_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database(); // Carga la base de datos en el constructor
    }

    public function find_by_id($id) {
        // Realiza la consulta para obtener el usuario por su ID
        $query = $this->db->get_where('subscriptions', array('id' => $id));
        // Retorna el resultado de la consulta
        return $query->row_array();
    }

    public function get_all_actives() {
        $query = $this->db->get_where('subscriptions', array('subscription_status' => 'active'));
        return $query->result();
    }

    public function insert($data) {
        // Inserta los datos del usuario en la base de datos
        $this->db->insert('subscriptions', $data);
        // Retorna el ID del último registro insertado
        return $this->db->insert_id();
    }
}