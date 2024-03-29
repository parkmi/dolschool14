<?php
/**
 * @version		$Id: default.php 22338 2011-11-04 17:24:53Z github_bot $
 * @package		Joomla.Site
 * @subpackage	mod_whosonline
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<?php if ($showmode == 0 || $showmode == 2) : ?>
	<?php $guest = JText::plural('MOD_WHOSONLINE_GUESTS', $count['guest']); ?>
	<?php $member = JText::plural('MOD_WHOSONLINE_MEMBERS', $count['user']); ?>
	<fieldset class="whosonline">
	<p><?php echo JText::sprintf('MOD_WHOSONLINE_WE_HAVE', $guest, $member); ?></p>
	</fieldset>
<?php endif; ?>

<?php if (($showmode > 0) && count($names)) : ?>
	<ul  class="whosonline<?php echo $moduleclass_sfx ?>" >
	<?php foreach($names as $name) : ?>
		<li>
			<?php if ($linknames == 1) : ?>
				<a href="index.php?option=com_users&view=profile&member_id=<?php echo (int) $name->userid; ?>">
				<?php echo $name->username; ?>
				</a>
			<?php else : ?>
				<?php echo $name->username; ?>
			<?php endif; ?>
		</li>
	<?php endforeach;  ?>
	</ul>
<?php endif;
