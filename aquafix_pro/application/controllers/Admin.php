<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_verificar_sesion();
        $this->load->model('Cliente_model');
        $this->load->model('Fontanero_model');
        $this->load->model('Servicio_model');
        $this->load->model('Solicitud_model');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('user_type') != 'admin') {
            redirect('login');
        }
    }

    public function dashboard() {
        $data = array(
            'title' => 'Panel de Administración',
            'total_clientes' => $this->Cliente_model->contar_clientes(),
            'total_fontaneros' => $this->Fontanero_model->contar_fontaneros(),
            'total_servicios' => $this->Servicio_model->contar_servicios(),
            'estadisticas_solicitudes' => $this->Solicitud_model->obtener_estadisticas()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/footer');
    }

    public function clientes() {
        $data = array(
            'title' => 'Gestión de Clientes',
            'clientes' => $this->Cliente_model->listar_todos()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('admin/clientes', $data);
        $this->load->view('templates/footer');
    }

    public function fontaneros() {
        $data = array(
            'title' => 'Gestión de Fontaneros',
            'fontaneros' => $this->Fontanero_model->listar_todos()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('admin/fontaneros', $data);
        $this->load->view('templates/footer');
    }

    public function servicios() {
        $data = array(
            'title' => 'Gestión de Servicios',
            'servicios' => $this->Servicio_model->listar_todos()
        );

        if ($this->input->method() === 'post') {
            $this->_guardar_servicio();
            return;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('admin/servicios', $data);
        $this->load->view('templates/footer');
    }

    private function _guardar_servicio() {
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
        $this->form_validation->set_rules('descripcion', 'Descripción', 'required');
        $this->form_validation->set_rules('precio_base', 'Precio', 'required|decimal');
        $this->form_validation->set_rules('duracion_estimada', 'Duración', 'required|integer');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required|in_list[reparacion,instalacion,mantenimiento,emergencia]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array(
                'success' => false,
                'errors' => $this->form_validation->error_array()
            ));
            return;
        }

        $datos = array(
            'nombre' => $this->input->post('nombre', TRUE),
            'descripcion' => $this->input->post('descripcion', TRUE),
            'precio_base' => $this->input->post('precio_base', TRUE),
            'duracion_estimada' => $this->input->post('duracion_estimada', TRUE),
            'tipo' => $this->input->post('tipo', TRUE),
            'disponible' => TRUE
        );

        $id_servicio = $this->input->post('id_servicio', TRUE);
        
        if ($id_servicio) {
            $resultado = $this->Servicio_model->actualizar($id_servicio, $datos);
            $mensaje = 'Servicio actualizado correctamente';
        } else {
            $resultado = $this->Servicio_model->crear($datos);
            $mensaje = 'Servicio creado correctamente';
        }

        if ($resultado) {
            echo json_encode(array('success' => true, 'message' => $mensaje));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al guardar el servicio'));
        }
    }

    public function solicitudes() {
        $filtros = array();
        
        if ($this->input->get('estado')) {
            $filtros['estado'] = $this->input->get('estado', TRUE);
        }

        $data = array(
            'title' => 'Gestión de Solicitudes',
            'solicitudes' => $this->Solicitud_model->listar_todas($filtros)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('admin/solicitudes', $data);
        $this->load->view('templates/footer');
    }

    public function asignar_fontanero() {
        header('Content-Type: application/json');

        $id_solicitud = $this->input->post('id_solicitud', TRUE);
        $id_fontanero = $this->input->post('id_fontanero', TRUE);
        $fecha_programada = $this->input->post('fecha_programada', TRUE);

        if (!$id_solicitud || !$id_fontanero || !$fecha_programada) {
            echo json_encode(array('success' => false, 'message' => 'Datos incompletos'));
            return;
        }

        if ($this->Solicitud_model->asignar_fontanero($id_solicitud, $id_fontanero, $fecha_programada)) {
            echo json_encode(array('success' => true, 'message' => 'Fontanero asignado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al asignar fontanero'));
        }
    }

    public function cambiar_estado_solicitud() {
        header('Content-Type: application/json');

        $id_solicitud = $this->input->post('id_solicitud', TRUE);
        $nuevo_estado = $this->input->post('estado', TRUE);

        if (!$id_solicitud || !$nuevo_estado) {
            echo json_encode(array('success' => false, 'message' => 'Datos incompletos'));
            return;
        }

        $estados_validos = array('pendiente', 'asignada', 'en_proceso', 'completada', 'cancelada');
        if (!in_array($nuevo_estado, $estados_validos)) {
            echo json_encode(array('success' => false, 'message' => 'Estado no válido'));
            return;
        }

        if ($this->Solicitud_model->actualizar_estado($id_solicitud, $nuevo_estado)) {
            echo json_encode(array('success' => true, 'message' => 'Estado actualizado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al actualizar el estado'));
        }
    }

    public function reportes() {
        $data = array(
            'title' => 'Reportes y Estadísticas',
            'estadisticas_solicitudes' => $this->Solicitud_model->obtener_estadisticas(),
            'mejores_fontaneros' => $this->Fontanero_model->obtener_mejores(10)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('admin/reportes', $data);
        $this->load->view('templates/footer');
    }
}
