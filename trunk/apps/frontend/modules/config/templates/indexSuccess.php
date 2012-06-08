<script>
<!--
var sort_by = '';
var sort_order = 0;
var current_view = '';
var page = 1;

function listReload(page)
{
    $('#indexAjax').html('<div align="center"><div class="loader_admin"></div></div>');
    $('#indexAjax').load('<?php echo url_for('@config') ?>?'+$('#filterSearch').serialize(), {
        sort_by:      sort_by,
        sort_order:   sort_order,
        page:         page,
        view:         current_view,
    });
}

function sort(type, default_order) {
    if (type != sort_by) {
        sort_by = type;
        sort_order = default_order;
    } else if(sort_order) {
        sort_order = 0
    } else {
        sort_order = 1;
    }
    listReload(1);
}
-->
</script>
<h1>Loads list</h1>
<div id="SearchForm" class='backend_form'>
  <form id="filterSearch" action="<?php echo url_for('@config') ?>" method="post">
    <table>
      <?php echo $config_form ?>
      <tr><th>
        <input  id="fs" type="submit" value="<?php echo "Search" ?>"/>
      </th></tr>
    </table>
  </form>
</div>
      
<div style='padding-top:10px;'>
<div class="UITable">
  <div id="indexAjax" class='filter_form'>
    <?php include 'indexAjax.php' ?>
  </div>
</div>

<a href="<?php echo url_for('@config_create') ?>"><img title="add new config" src="/images/add-icon.png" /></a>
</div>
