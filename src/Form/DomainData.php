<?php

/**
 * @file
 * Contains \Drupal\domain_data\Form\UserinfoForm.
*/

namespace Drupal\domain_data\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;

$storage = \Drupal::entityTypeManager()->getStorage('domain');
$domains = $storage->loadMultiple();

$domains_all = [];

foreach ($domains as $domain) {

  $did = $domain->get('id');

  $table = 'domain.config.'.$did.'.nebonew.settings';

  $domainConfig = \Drupal::config($table);

  $datas[$did]['hostname'] = $domain->getHostname();
  $datas[$did]['name'] = $domain->get('name');
  $datas[$did]['path'] = $domain->get('path');

  if(substr($datas[$did]['path'], -1) == '/') {
    $domain_url = substr($datas[$did]['path'], 0, -1);
  }
  $domains_all[] = $domain_url;
}

class DomainData extends FormBase {
	/**
	* {@inheritdoc}
	*/
	public function getFormId() {
		return 'domain_data_form';
	}

	/**
	* {@inheritdoc}
	*/

	public function buildForm(array $form, FormStateInterface $form_state) {
		$form['form_info'] = array(
			'#markup' => '<h4>Добавьте кастомные значения для домена</h4><br>',
		);

		$form['domain'] = array(
			'#type' => 'select',
			'#title' => t('Выберите домен'),
			'#options' => [1, 2, 3],
			'#required' => TRUE,
		);

		$form['base_domain'] = array(
			'#type'=>'checkbox',
			'#title'=>t('Корневой домен'),
		);

		$form['office_boolean'] = array(
			'#type'=>'checkbox',
			'#title'=>t('Наличие офиса'),
		);


		// Поля адреса
		$form['address'] = array(
			'#type' => 'fieldset',
			'#title' => t('Адрес'),
			'#description' => t(''),
			'#collapsible' => FALSE,
			'#collapsed' => TRUE,
		);

		$form['address']['region'] = array(
			'#type'=>'select',
			'#title'=>t('Регион'),
			'#options' => array(
				0 => 'Ставропольский край',
				1 => 'Краснодарский край',
				2 => 'Волгоградская область',
				3 => 'Карачаево-Черкесская республика'
			),
			'#required' => TRUE,
		);

		$form['address']['city'] = array(
			'#type' => 'textfield',
			'#title' => t('Город'),
			'#required' => TRUE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('Ставрополь')),
		);

		$form['address']['street'] = array(
			'#type' => 'textfield',
			'#title' => t('Улица'),
			'#required' => TRUE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('пр-т Кулакова')),
		);

		$form['address']['build'] = array(
			'#type' => 'textfield',
			'#title' => t('Номер дома'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('11/1')),
		);

		$form['address']['postal_code'] = array(
			'#type' => 'textfield',
			'#title' => t('Почтовый индекс'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('355000')),
		);

		$form['address']['descriptor'] = array(
			'#type' => 'textfield',
			'#title' => t('Дополнительное описание'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('ТД "Мой дом"')),
		);

		$form['address']['longitude'] = array(
			'#type' => 'textfield',
			'#title' => t('Долгота'),
			'#required' => TRUE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('45.043336')),
		);

		$form['address']['latitude'] = array(
			'#type' => 'textfield',
			'#title' => t('Широта'),
			'#required' => TRUE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('41.900362')),
		);

		$form['address']['work_time'] = array(
			'#type' => 'textfield',
			'#title' => t('Время работы'),
			'#required' => TRUE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('пн-пт 09:00-18:00')),
		);

		$form['address']['bus_stop'] = array(
			'#type' => 'textfield',
			'#title' => t('Название остановки'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('Промышленная')),
		);

		$form['address']['bus_route'] = array(
			'#type' => 'textfield',
			'#title' => t('Маршруты общественного транспорта'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('15, 18, 22, 13а')),
		);



		$form['multiple'] = [
			'#type' => 'element_multiple',
			'#title' => 'Адреса транспортных компаний',
			'#element' => [
			'delivery_name' => [
				'#type'=>'select',
				'#title'=>t('Регион'),
				'#options'=>array('CDEK','DPD','Деловые линии','ПЭК'),
				'#required' => TRUE,
			],
			'delivery_address' => [
				'#type' => 'textfield',
				'#title' => $this->t('Адрес'),
			],
		  ],
		  // '#default_value' => $config->get('multiple'),
		];
		
		

		// Поля контактов
		$form['contacts'] = array(
			'#type' => 'fieldset',
			'#title' => t('Контакты'),
			'#description' => t(''),
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,
		);

		$form['contacts']['phone'] = array(
			'#type' => 'textfield',
			'#title' => t('Телефон'),
			'#required' => TRUE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('+7(999) 999-99-99')),
		);

		$form['contacts']['phone_second'] = array(
			'#type' => 'textfield',
			'#title' => t('Телефон'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('+7(999) 999-99-99')),
		);

		$form['contacts']['email'] = array(
			'#type' => 'textfield',
			'#title' => t('Email'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('info@mail.ru')),
		);

		$form['contacts']['tg'] = array(
			'#type' => 'textfield',
			'#title' => t('Telegram'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('login')),
		);

		$form['contacts']['whatsapp'] = array(
			'#type' => 'textfield',
			'#title' => t('Whatsapp'),
			'#required' => FALSE,
			'#maxlength' => 50,
			'#default_value' => '',
			'#attributes' =>array('placeholder' => t('+79999999999')),
		);


		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = array(
			'#type' => 'submit',
			'#value' => $this->t('Save Info'),
			'#button_type' => 'primary',
		);
		return $form;
	}

	/**
	* {@inheritdoc}
	*/
	public function validateForm(array &$form, FormStateInterface $form_state) {
		if ($form_state->getValue('domain') == '') {
			$form_state->setErrorByName('domain', $this->t('Please Enter domain'));
		}
		if ($form_state->getValue('region') == '') {
			$form_state->setErrorByName('region', $this->t('Please Enter region'));
		}
		if ($form_state->getValue('city') == '') {
			$form_state->setErrorByName('city', $this->t('Please Enter city'));
		}
		if ($form_state->getValue('street') == '') {
			$form_state->setErrorByName('street', $this->t('Please Enter street'));
		}
		if ($form_state->getValue('longitude') == '') {
			$form_state->setErrorByName('longitude', $this->t('Please Enter longitude'));
		}
		if ($form_state->getValue('latitude') == '') {
			$form_state->setErrorByName('latitude', $this->t('Please Enter latitude'));
		}
		if ($form_state->getValue('work_time') == '') {
			$form_state->setErrorByName('work_time', $this->t('Please Enter work time'));
		}
		if ($form_state->getValue('phone') == '') {
			$form_state->setErrorByName('phone', $this->t('Please Enter phone'));
		}
	
	}

	/**
	* {@inheritdoc}
	*/
	public function submitForm(array &$form, FormStateInterface $form_state) {
		try{
			$conn = Database::getConnection();
			$field = $form_state->getValues();

			$fields["domain"] = $field['domain'];
			$fields["base_domain"] = $field['base_domain'];
			$fields["office_boolean"] = $field['office_boolean'];
			$fields["region"] = $field['region'];
			$fields["city"] = $field['city'];
			$fields["street"] = $field['street'];
			$fields["build"] = $field['build'];
			$fields["postal_code"] = $field['postal_code'];
			$fields["descriptor"] = $field['descriptor'];
			$fields["longitude"] = $field['longitude'];
			$fields["latitude"] = $field['latitude'];
			$fields["work_time"] = $field['work_time'];
			$fields["bus_stop"] = $field['bus_stop'];
			$fields["bus_route"] = $field['bus_route'];
			$fields["phone"] = $field['phone'];
			$fields["phone_second"] = $field['phone_second'];
			$fields["email"] = $field['email'];
			$fields["tg"] = $field['tg'];
			$fields["whatsapp"] = $field['whatsapp'];

			$conn->insert('domain_data')
			->fields($fields)->execute();
			\Drupal::messenger()->addMessage(t("User info has been succesfully saved. Уou can make changes to this data on the domain list page"));
		}
		catch(Exception $ex){
			\Drupal::logger('domain_data')->error($ex->getMessage());
		}
	}
}