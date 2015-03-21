<?php //echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/blogs/js/blogs.js', false); ?>


<div class="blogEntries form" ng-controller="Blogs">
	<div class="modal-header">BLOG</div>
	<div class="modal-body">
		<ul class="nav nav-tabs" role="tablist">
			<li ng-class="{active:tab.isSet(0)}" class="active">
				<a href="" role="tab" data-toggle="tab">
					<?php echo __('Edit') ?>
				</a>
			</li>
		</ul>
		<br />

		<div class="tab-content">

			<div class="panel panel-default">

				<?php echo $this->Form->create(
					'BlogEntry',
					array(
						'inputDefaults' => array(
							'div' => 'form-group'
						),
						'div' => 'form-control'
					)
				); ?>

				<!--		--><?php //echo $this->Form->hidden('Frame.id', array(
				//			'value' => $frameId,
				//		)); ?>

				<div class="panel-body">
					<fieldset>
						<?php
						echo $this->Form->input(
							'title',
							array(
								'label' => __d('blogs', 'Title')
							)
						);
						?>



						<div class="form-group">
							<label class="control-label">
								<?php echo __d('blogs', 'body1'); ?>
							</label>
							<?php echo $this->element('NetCommons.required'); ?>

							<div class="nc-wysiwyg-alert">
								<?php echo $this->Form->textarea(
									'body1',
									[
										'class' => 'form-control',
										'ui-tinymce' => 'tinymce.options',
										'ng-model' => 'blog_entry.body1',
										'rows' => 5,
										'required' => 'required',
									]
								) ?>
							</div>

							<?php echo $this->element(
								'NetCommons.errors',
								[
									'errors' => $this->validationErrors,
									'model' => 'BlogEntry',
									'field' => 'body1',
								]
							) ?>
						</div>

						<label><input type="checkbox" ng-model="writeBody2"/><?php echo __d('blogs', 'Write body2') ?>
						</label>

						<div class="form-group" ng-show="writeBody2">
							<label class="control-label">
								<?php echo __d('blogs', 'body2'); ?>
							</label>

							<div class="nc-wysiwyg-alert">
								<?php echo $this->Form->textarea(
									'body2',
									[
										'class' => 'form-control',
										'ui-tinymce' => 'tinymce.options',
										'ng-model' => 'blog_entry.body2',
										'rows' => 5,
									]
								) ?>
							</div>

							<?php echo $this->element(
								'NetCommons.errors',
								[
									'errors' => $this->validationErrors,
									'model' => 'BlogEntry',
									'field' => 'body2',
								]
							) ?>
						</div>

						<?php
						echo $this->Form->input(
							'published_datetime',
							array('label' => __d('blogs', 'Published datetime'))
						);

						echo $this->Form->input('blog_category_id', array('label' => __d('blogs', 'Category')));
						?>

						<div class="form-group">
							<label class="control-label">
								<?php echo __d('blogs', 'tag'); ?>
							</label>
							<input type="text" ng-model="newTag"/>
							<button type="button" class="btn btn-success btn-xs" ng-click="addTag()">
								<span class=""><?php echo __d('blogs', 'Add tag') ?></span>
							</button>

							<div>
								<div ng-repeat="tag in tags" class="badge">
									{{tag}}
									&nbsp;
									<button type="button" ng-click="removeTag(tag)" class="btn btn-xs">
										<span class="glyphicon glyphicon-remove small"><span
												class="sr-only">Remove tags</span> </span>
									</button>
									<input type="hidden" name="data[BlogTag][{{$index}}][name]" value="{{tag}}"/>
								</div>
							</div>
						</div>
					</fieldset>

				</div>

				<hr />

				<?php echo $this->element(
					'Comments.form',
					array('contentStatus' => $blogEntry['BlogEntry']['status'])
				); ?>

				<div class="panel-footer">

					<?php echo $this->element(
						'NetCommons.workflow_buttons',
						array('contentStatus' => $blogEntry['BlogEntry']['status'])
					); ?>
				</div>

				<?php echo $this->Form->end() ?>

			</div>

			<?php echo $this->element('Comments.index'); ?>

		</div>


	</div>

</div>
