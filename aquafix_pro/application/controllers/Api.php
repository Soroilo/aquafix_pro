<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Fontanero_model');
        $this->load->model('Servicio_model');
        $this->load->model('Solicitud_model');
        $this->load->model('Cliente_model');
        $this->load->model('Calificacion_model');
        header('Content-Type: application/json');
    }

    public function obtener_fontaneros_disponibles() {
        try {
            $fontaneros = $this->Fontanero_model->listar_disponibles();
            
            $datos = array();
            foreach ($fontaneros as $fontanero) {
                $datos[] = array(
                    'id' => $fontanero->id_fontanero,
                    'nombre' => $fontanero->nombre . ' ' . $fontanero->apellido,
                    'especialidad' => $fontanero->especialidad,
                    'rating' => $fontanero->rating_promedio,
                    'servicios' => $fontanero->servicios_completados,
                    'disponible' => $fontanero->disponible
                );
            }

            echo json_encode(array(
                'success' => true,
                'data' => $datos
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al obtener fontaneros'
            ));
        }
    }

    public function obtener_fontaneros_cercanos() {
        $lat = $this->input->post('latitud');
        $lng = $this->input->post('longitud');
        
        if (!$lat || !$lng) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Coordenadas no proporcionadas'
            ));
            return;
        }

        try {
            $fontaneros = $this->Fontanero_model->listar_disponibles();
            
            $datos = array();
            foreach ($fontaneros as $fontanero) {
                $distancia = $this->_calcular_distancia($lat, $lng, 4.7110, -74.0721);
                
                $datos[] = array(
                    'id' => $fontanero->id_fontanero,
                    'nombre' => $fontanero->nombre . ' ' . $fontanero->apellido,
                    'especialidad' => $fontanero->especialidad,
                    'rating' => $fontanero->rating_promedio,
                    'servicios' => $fontanero->servicios_completados,
                    'distancia' => round($distancia, 2),
                    'lat' => 4.7110 + (rand(-100, 100) / 10000),
                    'lng' => -74.0721 + (rand(-100, 100) / 10000)
                );
            }

            usort($datos, function($a, $b) {
                return $a['distancia'] <=> $b['distancia'];
            });

            echo json_encode(array(
                'success' => true,
                'data' => $datos
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al calcular distancias'
            ));
        }
    }

    private function _calcular_distancia($lat1, $lon1, $lat2, $lon2) {
        $radio_tierra = 6371;
        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);
        
        $a = sin($dlat/2) * sin($dlat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distancia = $radio_tierra * $c;
        
        return $distancia;
    }

    public function listar_servicios() {
        try {
            $servicios = $this->Servicio_model->listar_todos(TRUE);
            
            $datos = array();
            foreach ($servicios as $servicio) {
                $datos[] = array(
                    'id' => $servicio->id_servicio,
                    'nombre' => $servicio->nombre,
                    'descripcion' => $servicio->descripcion,
                    'precio' => $servicio->precio_base,
                    'duracion' => $servicio->duracion_estimada,
                    'tipo' => $servicio->tipo
                );
            }

            echo json_encode(array(
                'success' => true,
                'data' => $datos
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al obtener servicios'
            ));
        }
    }

    public function crear_solicitud() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Debe iniciar sesión para solicitar un servicio'
            ));
            return;
        }

        $this->form_validation->set_rules('id_servicio', 'Servicio', 'required|integer');
        $this->form_validation->set_rules('direccion_servicio', 'Dirección', 'required|max_length[200]');
        $this->form_validation->set_rules('descripcion_problema', 'Descripción', 'required|max_length[500]');
        $this->form_validation->set_rules('prioridad', 'Prioridad', 'required|in_list[baja,media,alta,urgente]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $this->form_validation->error_array()
            ));
            return;
        }

        $datos = array(
            'id_cliente' => $this->session->userdata('user_id'),
            'id_servicio' => $this->input->post('id_servicio', TRUE),
            'direccion_servicio' => $this->input->post('direccion_servicio', TRUE),
            'descripcion_problema' => $this->input->post('descripcion_problema', TRUE),
            'prioridad' => $this->input->post('prioridad', TRUE),
            'latitud' => $this->input->post('latitud'),
            'longitud' => $this->input->post('longitud')
        );

        try {
            $id_solicitud = $this->Solicitud_model->crear($datos);
            
            if ($id_solicitud) {
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Solicitud creada exitosamente',
                    'id_solicitud' => $id_solicitud
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Error al crear la solicitud'
                ));
            }
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ));
        }
    }

    public function actualizar_solicitud() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(array(
                'success' => false,
                'message' => 'No autorizado'
            ));
            return;
        }

        $id_solicitud = $this->input->post('id_solicitud', TRUE);
        $estado = $this->input->post('estado', TRUE);

        if (!$id_solicitud || !$estado) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Datos incompletos'
            ));
            return;
        }

        try {
            if ($this->Solicitud_model->actualizar_estado($id_solicitud, $estado)) {
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Estado actualizado correctamente'
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Error al actualizar el estado'
                ));
            }
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al procesar la actualización'
            ));
        }
    }

    public function enviar_calificacion() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('user_type') != 'cliente') {
            echo json_encode(array(
                'success' => false,
                'message' => 'No autorizado'
            ));
            return;
        }

        $this->form_validation->set_rules('id_solicitud', 'Solicitud', 'required|integer');
        $this->form_validation->set_rules('id_fontanero', 'Fontanero', 'required|integer');
        $this->form_validation->set_rules('puntuacion', 'Puntuación', 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]');
        $this->form_validation->set_rules('comentario', 'Comentario', 'max_length[500]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $this->form_validation->error_array()
            ));
            return;
        }

        $datos = array(
            'id_cliente' => $this->session->userdata('user_id'),
            'id_fontanero' => $this->input->post('id_fontanero', TRUE),
            'id_solicitud' => $this->input->post('id_solicitud', TRUE),
            'puntuacion' => $this->input->post('puntuacion', TRUE),
            'comentario' => $this->input->post('comentario', TRUE)
        );

        try {
            if ($this->Calificacion_model->crear($datos)) {
                $this->Fontanero_model->actualizar_rating($datos['id_fontanero']);

                echo json_encode(array(
                    'success' => true,
                    'message' => 'Calificación enviada exitosamente'
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Error al enviar la calificación'
                ));
            }
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al procesar la calificación'
            ));
        }
    }

    public function validar_email() {
        $email = $this->input->post('email', TRUE);
        
        if (!$email) {
            echo json_encode(array('available' => false));
            return;
        }

        $cliente = $this->Cliente_model->obtener_por_email($email);
        $disponible = ($cliente == null);

        echo json_encode(array('available' => $disponible));
    }
}
