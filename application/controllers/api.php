<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
    var $data; 
    public function __construct() {
        parent::__construct();
        $this->load->database(); // Carga la base de datos
        $this->load->model('client_model'); // Carga el modelo Usuario_model
        $this->load->model('plan_model'); // Carga el modelo Usuario_model
        $this->load->model('subscription_model'); // Carga el modelo Usuario_model
        $this->load->model('payment_model'); // Carga el modelo Usuario_model

        // Esto se podria meter en un helper
        $json = file_get_contents('php://input');
        $data = json_decode($json,true);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:

            break;
            case JSON_ERROR_DEPTH:
                die(' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                die( ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                die( ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                die( ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                die( ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                die( ' - Unknown error');
            break;
        }

        $this->data = $data;

    }

    public function index()
    {
        $data['usuarios'] = $this->client_model->obtener_clientes();

        //$data = array('id'=>1,'nombre'=>'Dante Caceres');
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * recibe un POST y crea una suscripcion 
     */
    public function subscription() {

        $client = $this->client_model->find_by_id($this->data['id_client']);
        $plan = $this->plan_model->find_by_id($this->data['id_plan']);
        if($client && $plan){
            $this->data['subscription_status'] = 'active';
            $this->data['id_subscription'] = $this->subscription_model->insert($this->data);
            
            $response = array('status' => 'success', 'message' => 'Subscription created successfully','data'=>$this->data);

        } else {
            $response = array('status' => 'failed', 'message' => 'Client or Plan do not exists');
        }

        // Enviar una respuesta JSON, si es apropiado
        echo json_encode($response);
    }

     /**
     * recibe un GET consulta detalle de un lote , parametro es el periodo
     */
    public function payments() {
        $period =$this->input->get('period');
        if(!$period) { 
            $period = date('Y-m').'-01';
        } else {
            $period .= '-01';
        }

        $payments = $this->payment_model->get_all_payments($period);
        foreach($payments as $key => $payment){
            $subscription = $this->subscription_model->find_by_id($payment->subscription_id);
            $client = $this->client_model->find_by_id($subscription['id_client']);
            $plan = $this->plan_model->find_by_id($subscription['id_plan']);
            $payments{$key}->subscription = $subscription;
            $payments{$key}->client = $client;
            $payments{$key}->plan = $plan;
        }

        $response = array(
            'period' => $period,
            'payments' => $payments
        );
      
        echo json_encode($response);
    }


    /**
     * recibe un GET consulta detalle de un lote , parametro es el periodo
     */
    public function payments_resume() {
        $period =$this->input->get('period');
        if(!$period) { 
            $period = date('Y-m').'-01';
        } else {
            $period .= '-01';
        }
        $payments = $this->payment_model->get_resume_payments($period);
        $response = array(
            'period' => $period,
            'stats' => $payments
        );
      
        echo json_encode($response);
    }

      /**
     * recibe un GET consulta detalle de un lote , parametro es el periodo
     */
    public function active_subscriptions() {
        
        $active_subscriptions = $this->subscription_model->get_all_actives();
        $response = array(
            'subscriptions' => $active_subscriptions
        );
      
        echo json_encode($response);
    }
}
