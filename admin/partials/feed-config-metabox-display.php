<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is display the custom feed configuration part of the metabox on feed edit screen.
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 *
 * @package    Rex_Product_Feed
 * @subpackage Rex_Product_Feed/admin/partials
 */

// Exit if $feed_template obj isn't available.
if ( ! isset($feed_template) ) {
  return;
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="rex-feed-config">
  <table id="config-table" class="responsive-table">
    <thead>
      <tr>
        <th class="large-col">Attributes</th>
        <th class="large-col">Type</th>
        <th class="large-col">Value</th>
        <th class="small-col">Prefix</th>
        <th class="small-col">Suffix</th>
        <th class="small-col">Output Sanitization</th>
        <th class="small-col">Output Limit</th>
      </tr>
    </thead>

    <tbody>

    <?php foreach ( $feed_template->getTemplateMappings() as $key => $item): ?>
      <tr data-row-id="<?php echo $key; ?>">
        <td><?php $feed_template->printSelectDropdown( $key, 'attr', $item['attr'] ); ?></td>
        <td><?php $feed_template->printAttType( $key, $item['type'] ); ?></td>
        <td>

          <div class="meta-dropdown">
            <?php $feed_template->printSelectDropdown( $key, 'meta_key', $item['meta_key'] ); ?>
          </div>

          <div class="static-input">
            <?php $feed_template->printInput( $key, 'st_value', $item['st_value'] ); ?>
          </div>

        </td>
        <td><?php $feed_template->printInput( $key, 'prefix', $item['prefix'] ); ?></td>
        <td><?php $feed_template->printInput( $key, 'suffix', $item['suffix'] ); ?></td>
        <td><?php $feed_template->printSelectDropdown( $key, 'escape', $item['escape'] ); ?></td>
        <td><?php $feed_template->printInput( $key, 'limit', $item['limit'] ); ?></td>
        <td>
          <a class="btn-floating waves-effect waves-light red delete">
            <i class="material-icons">delete</i>
          </a>
        </td>
      </tr>
    <?php endforeach ?>

    </tbody>

  </table>

  <br>

  <a id="rex-new-attr" class="waves-effect waves-light btn-large "><i class="material-icons left">add</i>Add New Attribute</a>

</div>
