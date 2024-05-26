<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\db\ActiveQuery;

/**
 * UserSearch represents the model behind the search form about `webvimark\modules\UserManagement\models\User`.
 */
class PatientSearch  extends Patient 
{

	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search($params)
	{
		$query = self::find();

		$query->with(["status", "polyclinic", "treatment", "formDisease", "updatedBy"]);

		/*if ( !Yii::$app->user->isSuperadmin )
		{
			$query->where(['superadmin'=>0]);
		}*/

        return $this->getDataProvider($query, $params);
    }

    public function restSearch($params): ActiveDataProvider
    {
        $query = self::find();

        $query->select(
            [
                'id',
                'name',
                'birthday',
                'phone',
                'polyclinic_id',
                'status_id',
                'treatment_id',
                'form_disease_id',
                'updated',
                'diagnosis_date',
                'recovery_date'
            ]
        );

        return $this->getDataProvider($query, $params, '');
    }

    /**
     * Метод возвращает Data provider из переданного запроса и поиска на основе params
     *
     * @param ActiveQuery $query
     * @param $params
     * @param string|null $formName
     *
     * @return ActiveDataProvider
     */
    private function getDataProvider(ActiveQuery $query, $params, string $formName = null): ActiveDataProvider
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 100),
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params, $formName) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'polyclinic_id' => $this->polyclinic_id,
            'status_id' => $this->status_id,
            'form_disease_id' => $this->form_disease_id,
            'treatment_id' => $this->treatment_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
