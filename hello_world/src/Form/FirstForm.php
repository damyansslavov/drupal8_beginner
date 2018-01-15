<?php

/**
 * @file
 * Contains \Drupal\hello_world\Form\FirstForm.
 */

namespace Drupal\hello_world\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a simple example form.
 */
class FirstForm extends FormBase {

  /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormID() {
    return 'first_form';
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // Use the Form API to define form elements.    
	$form['#title'] = $this->t('Programmatically create a node with our own form');
    $form['title'] = array(
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#maxlength' => 120,
    );
    $form['body'] = array(
      '#title' => t('Body'),
      '#type' => 'textarea',
    );
    $form['author'] = array(
    '#type' => 'textfield',
    '#title' => t('Author'),
    '#description' => t('Choose who the node should appear written by'),
    '#size' => 40,
    '#maxlength' => 60,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );
    return $form;
   /*$form['high_school']['tests_taken'] = array(
      '#type' => 'checkboxes',
	  '#options' => array(t('SAT'), t('ACT')),
      '#title' => t('What standardized tests did you take?'),
    );
	$form['pass'] = array(
      '#type' => 'password',
      '#title' => t('Password'),
      '#maxlength' => 64,
      '#size' => 15,
    );
    return $form;
	*/
  }

/**
   * Implements \Drupal\Core\Form\FormInterface::validateForm().
   */
  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {  
    // Validate the form values.
    $author = $form_state->getValue('author');
    if (preg_match('#[\d]#', $author)) {
      $form_state->setErrorByName('author', 'You need to submit a name, a name does not contain numbers');
      return FALSE;
    }
    else {
      return TRUE;
    }
 }

  /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // Do something useful.
    $title = $form_state->getValue('title');
    $body = $form_state->getValue('body');
    $author = $form_state->getValue('author');
    drupal_set_message('Your form was submitted successfully, you typed in the title ' . $title);
    drupal_set_message('Your form was submitted successfully, you typed in the body ' . $body);
    drupal_set_message('Your form was submitted successfully, you typed in the name ' . $author);
	
	// create node of type Article.
	$uid = db_query('SELECT uid from users_field_data where name = :name', array('name' => $form_state->getValue('author')))->fetchField();
    drupal_set_message('the users uid is ' . $uid);
    $node = entity_create('node', array(
      'type' => 'article',
      'title' => $form_state->getValue('title'),
      'body' => array(
        'value' => $form_state->getValue('body'),
        'format' => 'basic_html',
      ),
      'uid' => $uid,
    ));
    $node->save();
	$url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $node->id()]);	
	drupal_set_message('Your new node has been created and can be viewed by clicking the following url:');
	drupal_set_message(\Drupal::l(t('Click here to view your node'), $url)); 
  }

}