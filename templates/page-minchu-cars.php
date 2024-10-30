<?php
/**
 * Template Name: page-minchu-cars
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */
get_header();
?>

<?php
  $status = true;
  $domain = "https://www.min-chu.com";
  $json = wp_remote_get($domain . "/shop/carlist/api/" . get_option( 'minchu_api_key' ));
  $response_code = wp_remote_retrieve_response_code( $json );

  if ( 200 != $response_code) {
    $status = false;
    echo '<p class="wp_minchu_f-red wp_minchu_p30">みんちゅうAPI KEYが正しく設定されていない可能性があります。</p>';
  } 
  if ($status != false) {
    $data = json_decode($json['body']);
?>
<div class="wp_minchu_cars wp_minchu_wrapper">
  <?php 
      foreach($data->shopCarList as $item){
    ?>
  <div class="wp_minchu_item">
    <?php
     echo '<a href="' . get_permalink(get_option('page_minchu_detail')) . '?ucid=' . $item->usedCar->usedCrId . '" >';
    ?>
    <div class="wp_minchu_marks">
      <?php
        if($item->newFlag == "1" && $item->newFlag != "1"){
          echo '<span class="wp_minchu_bg-red">NEW</span>';
        } else if($item->updateFlag == "1" && $item->usedCar->createDatetime != $item->usedCar->updateDatetime){
          $upd = (int)$item->usedCar->updateDatetime / 1000;
          echo '<span class="wp_minchu_bg-red">' . date("Y/m/d",$upd) . '更新</span>';
        }
        $cons = $item->usedCar->cons;
        if($cons == "1"){
          echo '<span class="wp_minchu_bg-org">応談</span>';
        }
        $state = $item->usedCar->state;
        if($state == "1"){
          echo '<span class="wp_minchu_bg-grn">商談中</span>';
        }else if($state == "2"){
          echo '<span class="wp_minchu_bg-lbl">売約済み</span>';
        }
      ?>
    </div>
    <div class="wp_minchu_item_top">
      <div class="wp_minchu_car_img">
        <?php
             echo '<img class="wp_minchu_car_img" onerror="this.src=\'' . plugins_url('../img/noPhoto.png',__FILE__) . '\'" src="' . $domain . '/car_img/' . $data->shopCd . '/' .  $item->usedCar->usedCrId . '/1/' . $item->usedCarImg->imgPath . '" alt="車両画像は登録されていません">';
             ?>
      </div>
      <div class="wp_minchu_item_info">
        <h3 class="wp_minchu_item_info_car">
          <p class="wp_minchu_title_car"><?php echo $item->mkrNm ?> <?php echo $item->carNm ?></p>
        </h3>
        <div class="wp_minchu_item_info_meta">
          <span class="wp_minchu_meta01"><?php echo $item->carTypeNm ?></span>
          <span class="wp_minchu_meta01"><?php echo $item->usedCar->clr ?></span>
        </div>
        <div class="wp_minchu_item_info_meta">
          <span class="wp_minchu_meta02 wp_minchu_bd-org"><?php echo $item->factoryNm ?></span>
          <?php 
               $fa = $item->usedCar->freeAssure;
               if($fa == "1"){
                 echo '<span class="wp_minchu_meta02 wp_minchu_bd-bl">無償保証あり</span>';
               }
               $fa = $item->usedCar->paidAssure;
               if($fa == "1"){
                 echo '<span class="wp_minchu_meta02 wp_minchu_bd-grn">有償保証あり</span>';
               }
            ?>
        </div>
        <div class="wp_minchu_table01">
          <div class="wp_minchu_row">
            <div class="wp_minchu_price">
              <p class="wp_minchu_head">車両価格(税込)</p>
              <div class="wp_minchu_body">
                <p class="wp_minchu_body_inner">
                  <span class="wp_minchu_price_num">
                    <?php 
                       if($cons == "1"){
                         echo '<span class="wp_minchu_odan">応談</span>';
                       }else{
                         $price = $item->usedCar->price;
                         if($price == 0){
                           echo '0.0<span class="wp_minchu_sm">万円</span>';
                         }else{
                           echo $item->usedCar->price . '<span class="wp_minchu_sm">万円</span>';
                         }
                       }
                      ?>
                  </span>
                </p>
              </div>
            </div>
            <div class="wp_minchu_price">
              <p class="wp_minchu_head">総額(税込)</p>
              <div class="wp_minchu_body">
                <p class="wp_minchu_body_inner">
                  <span class="wp_minchu_price_num wp_minchu_f-red">
                    <?php 
                       if($cons == "1"){
                         echo "---";
                       }else{
                         $price = $item->usedCar->totalPrice;
                         if($price == 0){
                           echo '0.0<span class="wp_minchu_sm">万円</span>';
                         }else{
                           echo $price . '<span class="wp_minchu_sm">万円</span>';
                         }
                       }
                      ?>
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wp_minchu_table02">
      <div class="wp_minchu_row">
        <div class="wp_minchu_col">
          <p class="wp_minchu_head">年式</p>
          <p class="wp_minchu_body"><?php echo $item->ageType ?></p>
        </div>
        <div class="wp_minchu_col">
          <p class="wp_minchu_head">走行距離</p>
          <p class="wp_minchu_body"><?php echo number_format($item->usedCar->mile) ?>Km</p>
        </div>
        <div class="wp_minchu_col">
          <p class="wp_minchu_head">法定整備</p>
          <p class="wp_minchu_body"><?php echo $item->maintenanceNm ?></p>
        </div>
        <div class="wp_minchu_col">
          <p class="wp_minchu_head">車検</p>
          <div class="wp_minchu_body"><?php echo $item->carIns ?></div>
        </div>
        <div class="wp_minchu_col">
          <p class="wp_minchu_head">修復歴</p>
          <p class="wp_minchu_body"><?php echo $item->corrctNm ?></p>
        </div>
        <div class="wp_minchu_col">
          <p class="wp_minchu_head">地域</p>
          <p class="wp_minchu_body"><?php echo $item->tdfkNm ?></p>
        </div>
      </div>
    </div>
    <?php
      echo '</a>';
      ?>
  </div>
  <?php 
    }
  ?>
</div>
<?php 
 }
?>
<script>
  jQuery(function($) {
    $("body").animate({
      opacity: 1
    }, 1000);
  });

</script>
<?php get_footer(); ?>
