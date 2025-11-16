<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Fontanero_model');
        $this->load->model('Servicio_model');
        $this->load->model('Solicitud_model');
    }

    public function index() {
        $data = array(
            'title' => 'AquaFix Pro - Servicios de Fontanería',
            'fontaneros' => $this->Fontanero_model->listar_disponibles(),
            'servicios' => $this->Servicio_model->listar_todos(TRUE),
            'mejores_fontaneros' => $this->Fontanero_model->obtener_mejores(4)
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('templates/footer');
    }
}
