<div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img 
               src="<?= base_url('assets/uploads/user/') ?><?= $foto ?>" 
               class="img-circle elevation-2" 
               alt="User Image">
        </div>
        <div class="info">
        <?php $papelArray = array_map(function($obj) {
            return $obj->papel;  // Acessa o valor do atributo "papel"
          },  $papeis);  
          ?>
        <a href="#" class="d-block"><?= $nome ?><br><small><?= implode("|", $papelArray) ?></small></a>
        </div>
</div>