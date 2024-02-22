<?php
class Plan_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database(); // Carga la base de datos en el constructor
    }

    public function obtener_clientes() {
        $query = $this->db->get('plans');
        return $query->result_array(); // Retorna un arreglo de usuarios
    }

    public function find_by_id($id) {
        // Realiza la consulta para obtener el usuario por su ID
        $query = $this->db->get_where('plans', array('id' => $id));
        // Retorna el resultado de la consulta
        return $query->row_array();
    }
}