<?php

namespace Drupal\attribute\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\attribute\AttributeTypeInterface;

/**
 * Defines the Attribute type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "attribute_type",
 *   label = @Translation("Attribute Type"),
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\attribute\Form\Bundle",
 *       "edit" = "Drupal\attribute\Form\Bundle",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "list_builder" = "Drupal\attribute\ListBuilder\BundleListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer attribute",
 *   config_prefix = "type",
 *   bundle_of = "attribute_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/attribute/type/add",
 *     "edit-form" = "/admin/structure/attribute/type/{attribute_type}",
 *     "delete-form" = "/admin/structure/attribute/type/{attribute_type}/delete",
 *     "collection" = "/admin/structure/attribute/type",
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "description",
 *     "help",
 *     "new_revision"
 *   }
 * )
 */
class AttributeType extends ConfigEntityBundleBase implements AttributeTypeInterface {

  /**
   * The machine name of this attribute type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the attribute type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this attribute type.
   *
   * @var string
   */
  protected $description;

  /**
   * Help information shown to the user when creating a Attribute of this type.
   *
   * @var string
   */
  protected $help;

  /**
   * Default value of the 'Create new revision' checkbox of this attribute type.
   *
   * @var bool
   */
  protected $new_revision = TRUE;

  /**
   * Display setting for author and date Submitted by post information.
   *
   * @var bool
   */
  protected $display_submitted = TRUE;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    $locked = \Drupal::state()->get('attribute.type.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNewRevision() {
    return $this->new_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function setNewRevision($new_revision) {
    $this->new_revision = $new_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return $this->help;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Clear the attribute type cache to reflect the removal.
    $storage->resetCache(array_keys($entities));
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateNewRevision() {
    return $this->isNewRevision();
  }

}
