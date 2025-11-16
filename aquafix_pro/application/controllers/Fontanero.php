<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fontanero extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_verificar_sesion();
        $this->load->model('Fontanero_model');
        $this->load->model('Solicitud_model');
        $this->load->model('Calificacion_model');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('user_type') != 'fontanero') {
            redirect('login');
        }
    }

    public function dashboard() {
        $id_fontanero = $this->session->userdata('user_id');
        $fontanero = $this->Fontanero_model->obtener_por_id($id_fontanero);
        
        $data = array(
            'title' => 'Panel de Fontanero',
            'fontanero' => $fontanero,
            'estadisticas' => $this->Fontanero_model->obtener_estadisticas($id_fontanero),
            'solicitudes_pendientes' => $this->Solicitud_model->listar_por_fontanero($id_fontanero, array('estado' => 'asignada')),
            'calificaciones_recientes' => $this->Calificacion_model->listar_por_fontanero($id_fontanero)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('fontanero/dashboard', $data);
        $this->load->view('templates/footer');
    }

    public function solicitudes() {
        $id_fontanero = $this->session->userdata('user_id');
        $filtro_estado = $this->input->get('estado', TRUE);

        $filtros = array();
        if ($filtro_estado) {
            $filtros['estado'] = $filtro_estado;
        }

        $data = array(
            'title' => 'Mis Solicitudes',
            'solicitudes' => $this->Solicitud_model->listar_por_fontanero($id_fontanero, $filtros),
            'solicitudes_disponibles' => $this->Solicitud_model->obtener_pendientes()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('fontanero/solicitudes', $data);
        $this->load->view('templates/footer');
    }

    public function aceptar_solicitud() {
        header('Content-Type: application/json');

        $id_solicitud = $this->input->post('id_solicitud', TRUE);
        $fecha_programada = $this->input->post('fecha_programada', TRUE);

        if (!$id_solicitud || !$fecha_programada) {
            echo json_encode(array('success' => false, 'message' => 'Datos incompletos'));
            return;
        }

        $id_fontanero = $this->session->userdata('user_id');

        if ($this->Solicitud_model->asignar_fontanero($id_solicitud, $id_fontanero, $fecha_programada)) {
            echo json_encode(array('success' => true, 'message' => 'Solicitud aceptada exitosamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al aceptar solicitud'));
        }
    }

    public function iniciar_servicio($id) {
        header('Content-Type: application/json');

        $solicitud = $this->Solicitud_model->obtener_por_id($id);

        if (!$solicitud || $solicitud->id_fontanero != $this->session->userdata('user_id')) {
            echo json_encode(array('success' => false, 'message' => 'No autorizado'));
            return;
        }

        if ($this->Solicitud_model->actualizar_estado($id, 'en_proceso')) {
            echo json_encode(array('success' => true, 'message' => 'Servicio iniciado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al iniciar servicio'));
        }
    }

    public function completar($id) {
        header('Content-Type: application/json');

        $solicitud = $this->Solicitud_model->obtener_por_id($id);

        if (!$solicitud || $solicitud->id_fontanero != $this->session->userdata('user_id')) {
            echo json_encode(array('success' => false, 'message' => 'No autorizado'));
            return;
        }

        if ($this->Solicitud_model->completar($id)) {
            $this->Fontanero_model->incrementar_servicios($this->session->userdata('user_id'));
            $this->Fontanero_model->actualizar_rating($this->session->userdata('user_id'));
            echo json_encode(array('success' => true, 'message' => 'Servicio completado exitosamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al completar servicio'));
        }
    }

    public function perfil() {
        $id_fontanero = $this->session->userdata('user_id');
        $fontanero = $this->Fontanero_model->obtener_por_id($id_fontanero);

        if ($this->input->method() === 'post') {
            $this->_actualizar_perfil($id_fontanero);
            return;
        }

        $data = array(
            'title' => 'Mi Perfil',
            'fontanero' => $fontanero,
            'calificaciones' => $this->Calificacion_model->listar_por_fontanero($id_fontanero)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('fontanero/perfil', $data);
        $this->load->view('templates/footer');
    }

    private function _actualizar_perfil($id_fontanero) {
        header('Content-Type: application/json');

        $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[50]');
        $this->form_validation->set_rules('apellido', 'Apellido', 'required|max_length[50]');
        $this->form_validation->set_rules('telefono', 'Teléfono', 'required');
        $this->form_validation->set_rules('especialidad', 'Especialidad', 'required|max_length[100]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array(
                'success' => false,
                'message' => validation_errors(),
                'errors' => $this->form_validation->error_array()
            ));
            return;
        }

        $datos = array(
            'nombre' => $this->input->post('nombre', TRUE),
            'apellido' => $this->input->post('apellido', TRUE),
            'telefono' => $this->input->post('telefono', TRUE),
            'especialidad' => $this->input->post('especialidad', TRUE)
        );

        $password = $this->input->post('password');
        if (!empty($password)) {
            $datos['password'] = $password;
        }

        if ($this->Fontanero_model->actualizar($id_fontanero, $datos)) {
            echo json_encode(array(
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ));
        }
    }

    public function cambiar_disponibilidad() {
        header('Content-Type: application/json');

        $id_fontanero = $this->session->userdata('user_id');
        $disponible = $this->input->post('disponible', TRUE);

        if ($this->Fontanero_model->actualizar_disponibilidad($id_fontanero, $disponible)) {
            echo json_encode(array('success' => true, 'message' => 'Disponibilidad actualizada'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al actualizar'));
        }
    }
}
