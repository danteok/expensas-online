<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller {

  	public function __construct() {
        parent::__construct();
        $this->load->database(); // Carga la base de datos
        $this->load->model('client_model'); // Carga el modelo Usuario_model
        $this->load->model('plan_model'); // Carga el modelo Usuario_model
        $this->load->model('subscription_model'); // Carga el modelo Usuario_model
        $this->load->model('payment_model'); // Carga el modelo Usuario_model
    }

	/**
	 * Proceso que genera el lote de cobros, por default es el corriente mes
	 * @example php index.php  tools generate_payment_collections
	 */

	public function generate_payments($period = '')
	{
		if(!$period){
			$period = date('Y-m');
		}
		// recorrer todos las subscriptions activas
		$subscriptions = $this->subscription_model->get_all_actives();
		foreach ($subscriptions as $subscription) {
			// para cada una :
				// 1 - verificar que no exista previamente con fecha actual en tabla payment_collections
				// 2 - si no existe,  insertarla
				if(!$this->payment_model->checkIfExists($subscription->id, $period)) {
					$plan = $this->plan_model->find_by_id($subscription->id_plan);

					$payment = array(
						'subscription_id' => $subscription->id,
						'period'		=> $period . '-01',
						'amount'		=> $plan['price'],
						'payment_type'  => $subscription->payment_type,
						'status'        => 'generado'
					);
					$this->payment_model->insert($payment);
					echo "Subscription: ".$subscription->id." for period:".$period." was created! <br>"; 
				}

		}
		echo "end!"; 
	}
}
?>