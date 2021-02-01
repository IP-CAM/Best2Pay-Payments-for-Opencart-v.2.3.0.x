<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-best2pay_e" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <br/>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-best2pay_e" class="form-horizontal">

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sector"><span data-toggle="tooltip" title="<?php echo $help_sector; ?>"><?php echo $entry_sector; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="best2pay_e_sector" value="<?php echo $best2pay_e_sector; ?>" id="input-sector" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-password"><span data-toggle="tooltip" title="<?php echo $help_password; ?>"><?php echo $entry_password; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="best2pay_e_password" value="<?php echo $best2pay_e_password; ?>" id="input-sector" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-test"><span data-toggle="tooltip" title="<?php echo $help_test; ?>"><?php echo $entry_test; ?></span></label>
            <div class="col-sm-10">
				<select name="best2pay_e_test">
				<?php if ($best2pay_e_test == '0') { ?>
				<option value="0" selected="selected"><?php echo $text_off; ?></option>
				<?php } else { ?>
				<option value="0"><?php echo $text_off; ?></option>
				<?php } ?>
				<?php if ($best2pay_e_test == '1') { ?>
				<option value="1" selected="selected"><?php echo $text_on; ?></option>
				<?php } else { ?>
				<option value="1"><?php echo $text_on; ?></option>
				<?php } ?>
				</select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="best2pay_e_status" id="input-status" class="form-control">
                <?php if ($best2pay_e_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="best2pay_e_sort_order" value="<?php echo $best2pay_e_sort_order; ?>" id="input-sort-order" class="form-control"/>
            </div>
          </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-kkt"><span data-toggle="tooltip" title="<?php echo $help_kkt; ?>"><?php echo $entry_kkt; ?></span></label>
                <div class="col-sm-10">
                    <input type="text" name="best2pay_e_kkt" value="<?php echo $best2pay_e_kkt; ?>" id="input-kkt" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-tax"><span data-toggle="tooltip" title="<?php echo $help_tax; ?>"><?php echo $entry_tax; ?></span></label>
                <div class="col-sm-10">
                    <input type="text" name="best2pay_e_tax" value="<?php echo $best2pay_e_tax; ?>" id="input-tax" class="form-control" />
                </div>
            </div>

        </form>

      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
