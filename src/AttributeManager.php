<?php

namespace Drupal\attribute;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\attribute\Plugin\attribute\PluginManager;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Class AttributeManager
 * @package Drupal\attribute
 */
class AttributeManager {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public $entityTypeManager;

  /**
   * @var \Drupal\attribute\Plugin\attribute\PluginManager
   */
  public $matcherPluginManager;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  public $cache;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  public $configFactory;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  public $moduleHandler;

  /**
   * The current user injected into the service.
   *
   * @var AccountInterface
   */
  public $currentUser;

  /**
   * The session manager.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  public $sessionManager;

  /**
   * @var \Drupal\field\FieldConfigStorage
   */
  public $fieldConfigStorage;

  /**
   * @var \Drupal\field\FieldStorageConfigStorage
   */
  public $fieldStorageConfigStorage;

  /**
   * AttributeManager constructor.
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param PluginManager $matcher_plugin_manager
   * @param CacheBackendInterface $cache
   * @param ConfigFactoryInterface $config_factory
   * @param ModuleHandlerInterface $module_handler
   * @param AccountInterface $current_user
   * @param SessionManagerInterface $session_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, PluginManager $matcher_plugin_manager, CacheBackendInterface $cache, ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, AccountInterface $current_user, SessionManagerInterface $session_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->matcherPluginManager = $matcher_plugin_manager;
    $this->cache = $cache;
    $this->configFactory = $config_factory;
    $this->moduleHandler = $module_handler;
    $this->currentUser = $current_user;
    $this->sessionManager = $session_manager;

    $this->fieldConfigStorage = $this->entityTypeManager
      ->getStorage('field_config');

    $this->fieldStorageConfigStorage = $this->entityTypeManager
      ->getStorage('field_storage_config');
  }

  /**
   * Build a list of attribute fields.
   *
   * @return array
   */
  public function getAttributeFields() {

    $cid = 'attribute_field_list';
    $data = $this->cache->get($cid);

    if (empty($data)) {
      $data = [];
      /**
       * @var string $key
       * @var \Drupal\field\FieldConfigInterface $field
       */
      foreach ($this->fieldConfigStorage->loadByProperties(['field_type' => 'entity_reference']) as $key => $field) {

        $entity_type = $field->getTargetEntityTypeId();
        $bundle = $field->getTargetBundle();
        $field_name = $field->getName();

        // Check that the entity_reference field targets the attribute entity type.
        $attribute_entity_type = $field->getSetting('target_type');
        if ($attribute_entity_type != 'attribute') {
          continue;
        }
        if ($field->getTargetEntityTypeId() == 'taxonomy_term') {
          array_unshift([$entity_type][$bundle][$field_name], $field);
        }
        else {
          $data[$entity_type][$bundle][$field_name] = $field;
        }
      }

      $this->cache->set($cid, $data);
    }
    else {
      $data = $data->data;
    }

    return $data;
  }

}
