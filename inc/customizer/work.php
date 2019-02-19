<?php
/**
 * Render the edit work experience view
 * @var $view \helpers\Core\View
 */

/**
 * Get the user
 * @var $user \models\User
 */
$user = $view->get('user');
$industries = $view->get('industries');
$specializations = $view->get('specializations');
// Get user experience
$user_experience = $user->getProfile()->getProfileMeta()->getExperience();
$total_experience = count($user_experience);

// Get the first item
$first_experience = current($user_experience);
// Remove the first one from the list afterwards
if( $first_experience && is_array($first_experience)) { unset($user_experience[$first_experience['user_experience_id']]); }
?>
	<div class="card-body">
		<?php
		if($first_experience) {
			$view->render('/profile/components/forms/work/work', ['experience'  => $first_experience, 'industries' => $industries, 'specializations' => $specializations]);
		}
		?>

        <div id="add-work-accordion" class="collapse add-item-accordian" aria-labelledby="headingOne" data-parent="#accordion">
			<?php $view->render('/profile/components/forms/add-3-col', ['type' => 'experience', 'industries' => $industries, 'specializations' => $specializations]) ?>
		</div>

        <div id="experience-accordion" class="collapse extra-items" aria-labelledby="headingOne" data-parent="#accordion">
			<?php
			if( empty($user_experience) === false ) {
				foreach($user_experience as $k=>$experience) {
					$view->render('/profile/components/forms/work/work', ['experience'    => $experience, 'industries' => $industries, 'specializations' => $specializations]);
				}
			}
			?>
		</div>
	</div>

    <div class="card-footer bg-transparent flex justify-content-end <?= ($total_experience <= 1) ? 'hide' : ''?>">
        <a href="#" data-toggle="collapse" data-target="#experience-accordion" aria-expanded="true" aria-controls="experience-accordion">
            View More
            <i class="fas fa-angle-down" style="margin-left:8px;"></i>
        </a>
    </div>
