<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "modulos".
 *
 * @property int $id
 * @property string $nombre
 * @property int $habitacion_id
 * @property int $tipo_id
 * @property int $icono_id
 * @property int $estado
 *
 * @property Habitaciones $habitacion
 * @property Tipos $tipo
 */
class Modulos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modulos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'habitacion_id', 'tipo_id'], 'required'],
            [['habitacion_id', 'tipo_id', 'icono_id', 'estado'], 'default', 'value' => null],
            [['habitacion_id', 'tipo_id', 'icono_id', 'estado'], 'integer'],
            [['nombre'], 'string', 'length' => [4, 20]],
            [
                ['nombre'],
                'unique',
                'targetAttribute' => ['nombre', 'habitacion_id'],
                'message' => 'El módulo \'{value}\' ya existe en este habitación',
            ],
            [
                ['habitacion_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Habitaciones::className(),
                'targetAttribute' => ['habitacion_id' => 'id']
            ],
            [
                ['habitacion_id'], function ($attribute, $params, $validator) {
                    $habitacion = Habitaciones::findOne(['id' => $this->$attribute]);
                    if ($habitacion->seccion->usuario_id !== Yii::$app->user->identity->id) {
                        $this->addError($attribute, 'Habitación no válida');
                    }
                }
            ],
            [
                ['tipo_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Tipos::className(),
                'targetAttribute' => ['tipo_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'habitacion_id' => 'Habitacion',
            'tipo_id' => 'Tipo de módulo',
            'icono_id' => 'Icono',
        ];
    }

    /**
     * Devuelve verdadero si el módulo pertenece al usuario logueado.
     * @return bool Si pertenece al usuario o no.
     */
    public function getEsPropia()
    {
        return $this->usuario->id == Yii::$app->user->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])
            ->via('seccion');
    }


    /**
    * @return \yii\db\ActiveQuery
     */
    public function getSeccion()
    {
        return $this->hasOne(Secciones::className(), ['id' => 'seccion_id'])
            ->via('habitacion');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHabitacion()
    {
        return $this->hasOne(Habitaciones::className(), ['id' => 'habitacion_id'])
            ->inverseOf('modulos');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Tipos::className(), ['id' => 'tipo_id'])->inverseOf('modulos');
    }
}