<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AntropometriaItemsSeeder extends Seeder
{
    public function run()
    {
        $tipoPesquisaId = 1;
        $db = $this->db;

        $db->table('tipo_pesquisa')->where('idtipo_pesquisa', $tipoPesquisaId)->update([
            'nombre_tipo' => 'ANTROPOMETRIA',
            'descripcion_view' => 'Antropometría',
        ]);

        $db->table('pesquisa_items')->where('tipo_pesquisa_id', $tipoPesquisaId)->delete();

        $rows = [
            ['metodo_medicion_talla','Método de medición de talla','mediciones_basicas','select','["de_pie","acostado"]',0,1,'',null,null,'Solo aplica para menores o iguales a 730 días',null,null,6],
            ['peso','Peso','mediciones_basicas','number',null,1,2,'kg',0.9,275,'Peso en kg',null,null,6],
            ['talla','Talla / longitud','mediciones_basicas','number',null,1,3,'cm',30,230,'Talla en cm',null,null,6],
            ['edema','Edema','mediciones_basicas','boolean',null,1,4,'',null,null,null,null,null,6],
            ['imc','IMC','mediciones_basicas','number',null,0,5,'kg/m²',null,null,null,null,null,6],
            ['circ_cintura','Circunferencia de cintura','mediciones_basicas','number',null,0,6,'cm',30,220,'Obligatoria en adultos',null,null,4],
            ['circ_cefalica','Circunferencia cefálica','circunferencias','number',null,0,1,'cm',20,80,null,null,null,4],
            ['circ_brazo_izq','Circunferencia brazo izquierdo','circunferencias','number',null,0,2,'cm',5,60,null,null,null,4],
            ['pliegue_tricipital','Pliegue tricipital','pliegues','number',null,0,1,'mm',1,80,null,null,null,4],
            ['pliegue_subescapular','Pliegue subescapular','pliegues','number',null,0,2,'mm',1,80,null,null,null,4],
            ['zpe','Z-score Peso/Edad','percentiles','number',null,0,1,'Z',null,null,null,null,null,3],
            ['zpe_percentil','Percentil Peso/Edad','percentiles','text',null,0,2,'',null,null,null,null,null,3],
            ['zte','Z-score Talla/Edad','percentiles','number',null,0,3,'Z',null,null,null,null,null,3],
            ['zte_percentil','Percentil Talla/Edad','percentiles','text',null,0,4,'',null,null,null,null,null,3],
            ['zpt','Z-score Peso/Talla o Peso/Longitud','percentiles','number',null,0,5,'Z',null,null,null,null,null,3],
            ['zpt_percentil','Percentil Peso/Talla o Peso/Longitud','percentiles','text',null,0,6,'',null,null,null,null,null,3],
            ['zimce','Z-score IMC/Edad','percentiles','number',null,0,7,'Z',null,null,null,null,null,3],
            ['zimce_percentil','Percentil IMC/Edad','percentiles','text',null,0,8,'',null,null,null,null,null,3],
            ['zcc','Z-score CC/Edad','percentiles','number',null,0,9,'Z',null,null,null,null,null,3],
            ['zcc_percentil','Percentil CC/Edad','percentiles','text',null,0,10,'',null,null,null,null,null,3],
            ['zcbi','Z-score CBI/Edad','percentiles','number',null,0,11,'Z',null,null,null,null,null,3],
            ['zcbi_percentil','Percentil CBI/Edad','percentiles','text',null,0,12,'',null,null,null,null,null,3],
            ['zptri','Z-score PT/Edad','percentiles','number',null,0,13,'Z',null,null,null,null,null,3],
            ['zptri_percentil','Percentil PT/Edad','percentiles','text',null,0,14,'',null,null,null,null,null,3],
            ['zpsub','Z-score PS/Edad','percentiles','number',null,0,15,'Z',null,null,null,null,null,3],
            ['zpsub_percentil','Percentil PS/Edad','percentiles','text',null,0,16,'',null,null,null,null,null,3],
            ['grupo_edad_reporte','Grupo de edad para reporte','percentiles','text',null,0,17,'',null,null,null,null,null,6],
            ['clasificacion_imc_talla','Clasificación plataforma','percentiles','text',null,0,18,'',null,null,null,null,null,6],
            ['estado_nutricional_agregado','Estado nutricional agregado','percentiles','text',null,0,19,'',null,null,null,null,null,6],
            ['edad_dias_medicion','Edad en días al medir','percentiles','number',null,0,20,'días',null,null,null,null,null,3],
            ['edad_meses_medicion','Edad en meses al medir','percentiles','number',null,0,21,'meses',null,null,null,null,null,3],
            ['embarazada','Embarazada','condiciones','boolean',null,0,1,'',null,null,null,null,null,4],
            ['lactante','Mujer lactante','condiciones','boolean',null,0,2,'',null,null,null,null,null,4],
            ['embarazo_fum','Fecha última menstruación','condiciones','date',null,0,3,'',null,null,null,'embarazada','1',4],
            ['embarazo_fecha_eco','Fecha último eco','condiciones','date',null,0,4,'',null,null,null,'embarazada','1',4],
            ['embarazo_semanas_eco','Semanas indicadas por eco','condiciones','number',null,0,5,'sem',0,45,null,'embarazada','1',4],
            ['embarazo_semanas','Semanas de embarazo calculadas','condiciones','number',null,0,6,'sem',0,45,null,'embarazada','1',4],
            ['embarazo_imc_pregestacional','IMC pregestacional','condiciones','number',null,0,7,'kg/m²',null,null,null,'embarazada','1',4],
            ['embarazo_ganancia_kg','Ganancia de peso','condiciones','number',null,0,8,'kg',null,null,null,'embarazada','1',4],
            ['discapacidad','Discapacidad','estimacion_talla','boolean',null,0,1,'',null,null,null,null,null,4],
            ['se_mantiene_erguido','Se mantiene erguido','estimacion_talla','boolean',null,0,2,'',null,null,null,'discapacidad','1',4],
            ['ausencia_extremidades','Ausencia de extremidades','estimacion_talla','boolean',null,0,3,'',null,null,null,'discapacidad','1',4],
            ['talla_estimada','Talla estimada','estimacion_talla','number',null,0,4,'cm',30,230,null,'discapacidad','1',4],
            ['peso_ajustado','Peso ajustado','estimacion_talla','number',null,0,5,'kg',0.9,275,null,'discapacidad','1',4],
            ['remision','Remisión','tratamiento','select','["","nutricion","medicina_general","pediatria","gineco_obstetricia"]',0,1,'',null,null,null,null,null,6],
        ];

        $insert = [];
        foreach ($rows as $r) {
            $insert[] = [
                'tipo_pesquisa_id' => $tipoPesquisaId,
                'codigo' => $r[0],
                'nombre' => $r[1],
                'seccion' => $r[2],
                'tipo_dato' => $r[3],
                'opciones_json' => $r[4],
                'obligatorio' => $r[5],
                'orden' => $r[6],
                'unidad' => $r[7],
                'valor_min' => $r[8],
                'valor_max' => $r[9],
                'placeholder' => $r[10],
                'depende_de' => $r[11],
                'depende_valor' => $r[12],
                'ancho_col' => $r[13],
                'status_item' => 1,
            ];
        }

        $db->table('pesquisa_items')->insertBatch($insert);
    }
}
