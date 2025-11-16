<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_verificar_sesion();
        $this->load->model('Cliente_model');
        $this->load->model('Solicitud_model');
        $this->load->model('Servicio_model');
        $this->load->model('Fontanero_model');
        $this->load->library('form_validation'); 
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('user_type') != 'cliente') {
            redirect('login');
        }
    }

    public function dashboard() {
        $id_cliente = $this->session->userdata('user_id');
        
        $data = array(
            'title' => 'Panel de Cliente',
            'estadisticas' => $this->Cliente_model->obtener_estadisticas($id_cliente),
            'solicitudes_recientes' => $this->Solicitud_model->listar_por_cliente($id_cliente, array('limit' => 5)),
            'servicios_populares' => $this->Servicio_model->obtener_populares(4)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/dashboard', $data);
        $this->load->view('templates/footer');
    }

    public function solicitar_servicio() {
        $data = array(
            'title' => 'Solicitar Servicio',
            'servicios' => $this->Servicio_model->listar_todos(TRUE),
            'fontaneros' => $this->Fontanero_model->listar_disponibles()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/solicitar', $data);
        $this->load->view('templates/footer');
    }

    public function crear_solicitud() {
        if ($this->input->method() !== 'post') {
            echo json_encode(array(
                'success' => false,
                'message' => 'Método no permitido'
            ));
            return;
        }

        $this->form_validation->set_rules('id_servicio', 'Servicio', 'required|numeric');
        $this->form_validation->set_rules('direccion_servicio', 'Dirección', 'required|max_length[200]');
        $this->form_validation->set_rules('descripcion_problema', 'Descripción', 'required');
        $this->form_validation->set_rules('prioridad', 'Prioridad', 'required|in_list[baja,media,alta,urgente]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Por favor completa todos los campos correctamente',
                'errors' => validation_errors()
            ));
            return;
        }

        $id_cliente = $this->session->userdata('user_id');
        
        $datos_solicitud = array(
            'id_cliente' => $id_cliente,
            'id_servicio' => $this->input->post('id_servicio', TRUE),
            'direccion_servicio' => $this->input->post('direccion_servicio', TRUE),
            'descripcion_problema' => $this->input->post('descripcion_problema', TRUE),
            'prioridad' => $this->input->post('prioridad', TRUE)
        );

        $resultado = $this->Solicitud_model->crear($datos_solicitud);

        if ($resultado) {
            echo json_encode(array(
                'success' => true,
                'message' => '✅ Solicitud creada exitosamente',
                'id_solicitud' => $resultado
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al crear la solicitud. Intenta nuevamente.'
            ));
        }
    }

    public function mis_solicitudes() {
        $id_cliente = $this->session->userdata('user_id');
        $filtro_estado = $this->input->get('estado', TRUE);

        $filtros = array();
        if ($filtro_estado) {
            $filtros['estado'] = $filtro_estado;
        }

        $data = array(
            'title' => 'Mis Solicitudes',
            'solicitudes' => $this->Solicitud_model->listar_por_cliente($id_cliente, $filtros)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/solicitudes', $data);
        $this->load->view('templates/footer');
    }

    public function detalle_solicitud($id) {
        $solicitud = $this->Solicitud_model->obtener_por_id($id);
        
        if (!$solicitud || $solicitud->id_cliente != $this->session->userdata('user_id')) {
            show_404();
            return;
        }

        $data = array(
            'title' => 'Detalle de Solicitud',
            'solicitud' => $solicitud
        );

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/detalle', $data);
        $this->load->view('templates/footer');
    }

    public function perfil() {
        $id_cliente = $this->session->userdata('user_id');
        $cliente = $this->Cliente_model->obtener_por_id($id_cliente);

        if ($this->input->method() === 'post') {
            $this->_actualizar_perfil($id_cliente);
            return;
        }

        $data = array(
            'title' => 'Mi Perfil',
            'cliente' => $cliente
        );

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/perfil', $data);
        $this->load->view('templates/footer');
    }

    private function _actualizar_perfil($id_cliente) {
        header('Content-Type: application/json');

        $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[50]');
        $this->form_validation->set_rules('apellido', 'Apellido', 'required|max_length[50]');
        $this->form_validation->set_rules('telefono', 'Teléfono', 'required');
        $this->form_validation->set_rules('direccion', 'Dirección', 'required|max_length[200]');

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
            'direccion' => $this->input->post('direccion', TRUE)
        );

        $password = $this->input->post('password');
        if (!empty($password)) {
            $datos['password'] = $password;
        }

        if ($this->Cliente_model->actualizar($id_cliente, $datos)) {
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

    public function cancelar_solicitud($id) {
        header('Content-Type: application/json');

        $solicitud = $this->Solicitud_model->obtener_por_id($id);

        if (!$solicitud || $solicitud->id_cliente != $this->session->userdata('user_id')) {
            echo json_encode(array('success' => false, 'message' => 'No autorizado'));
            return;
        }

        if ($this->Solicitud_model->cancelar($id)) {
            echo json_encode(array('success' => true, 'message' => 'Solicitud cancelada exitosamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al cancelar la solicitud'));
        }
    }

    public function calificar() {
        header('Content-Type: application/json');

        $id_solicitud = $this->input->post('id_solicitud', TRUE);
        $id_fontanero = $this->input->post('id_fontanero', TRUE);
        $puntuacion = $this->input->post('puntuacion', TRUE);
        $comentario = $this->input->post('comentario', TRUE);

        if (!$id_solicitud || !$id_fontanero || !$puntuacion) {
            echo json_encode(array('success' => false, 'message' => 'Datos incompletos'));
            return;
        }

        if ($puntuacion < 1 || $puntuacion > 5) {
            echo json_encode(array('success' => false, 'message' => 'La puntuación debe estar entre 1 y 5'));
            return;
        }

        $solicitud = $this->Solicitud_model->obtener_por_id($id_solicitud);
        if (!$solicitud || $solicitud->id_cliente != $this->session->userdata('user_id')) {
            echo json_encode(array('success' => false, 'message' => 'No autorizado'));
            return;
        }

        if ($solicitud->estado !== 'completada') {
            echo json_encode(array('success' => false, 'message' => 'Solo puedes calificar servicios completados'));
            return;
        }

        $this->load->model('Calificacion_model');

        $datos = array(
            'id_solicitud' => $id_solicitud,
            'id_fontanero' => $id_fontanero,
            'id_cliente' => $this->session->userdata('user_id'),
            'puntuacion' => $puntuacion,
            'comentario' => $comentario
        );

        if ($this->Calificacion_model->crear($datos)) {
            echo json_encode(array('success' => true, 'message' => 'Calificación enviada exitosamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al guardar la calificación'));
        }
    }
}