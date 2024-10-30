<?php
/**
 * Template Name: page-minchu-car
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
  try{
    if(!isset($_GET["ucid"])){
      echo '<p class="f-red p30">車両番号がパラメータに設定されていません。</p>';
      throw new Exception("UCID未設定");
    }
    $json = wp_remote_get($domain . "/shop/cardetail/api/" . get_option( 'minchu_api_key' ) . "/" . $_GET["ucid"]);
    $response_code = wp_remote_retrieve_response_code( $json );

    if ( 200 != $response_code) {
      $status = false;
      echo '<p class="wp_minchu_f-red wp_minchu_p30">みんちゅうAPI KEYが正しく設定されていない可能性があります。</p>';
    }
  }catch(Exception $ex){
    $status = false;
  }
  if ($status != false) {
    $data = json_decode($json['body']);
?>
<div class="wp_minchu_car wp_minchu_wrapper">
  <?php
echo '<a href="' . get_permalink(get_option('page_minchu_list')) . '" class="wp_minchu_back">◀︎車両一覧に戻る</a>';
     ?>
  <div class="wp_minchu_marks">
    <?php
      if($data->carDetail->newFlag == "1"){
        echo '<span class="wp_minchu_bg-red">NEW</span>';
      } else if($data->carDetail->updateFlag == "1" && $data->carDetail->usedCar->createDatetime != $data->carDetail->usedCar->updateDatetime){
        $upd = (int)$data->carDetail->usedCar->updateDatetime / 1000;
        echo '<span class="wp_minchu_bg-red">' . date("Y/m/d",$upd) . "更新</span>";
      } 
      
      $cons = $data->carDetail->usedCar->cons;
      if($cons == "1"){
        echo '<span class="wp_minchu_bg-org">応談</span>';
      }
      $state = $data->carDetail->usedCar->state;
      if($state == "1"){
        echo '<span class="wp_minchu_bg-grn">商談中</span>';
      }else if($state == "2"){
        echo '<span class="wp_minchu_bg-lbl">売約済み</span>';
      }
      $fa = $data->carDetail->usedCar->freeAssure;
      $pa = $data->carDetail->usedCar->paidAssure;
      $ma = $data->carDetail->usedCar->maintenance;
      if($fa == "1"){
        echo '<span class="wp_minchu_bd-bl">無償保証あり</span>';
      }
      if($pa == "1"){
        echo '<span class="wp_minchu_bd-grn">有償保証あり</span>';
      }
     if($ma != "1"){
       echo '<span class="wp_minchu_bd-red">' . $data->carDetail->maintenanceNm  . "</span>";
     }
     ?>
  </div>
  <h2 class="wp_minchu_car_name"><?php echo $data->carDetail->mkrNm ?> <?php echo $data->carDetail->carNm ?> <br class="wp_minchu_sp"><span class="wp_minchu_grd"><?php echo $data->carDetail->gradeNm ?></span></h2>
  <section class="wp_minchu_section01">
    <div class="wp_minchu_left">
      <div class="wp_minchu_car_img">
        <?php
          if(count($data->imgs)==0){
            echo '<img class="wp_minchu_car_img_main" src="' . plugins_url('../img/noPhoto.png',__FILE__) . '">';
          }else{
           $errImg = 'this.src="' . plugins_url('../img/noPhoto.png', __FILE__) . '\'"';
           echo '<img class="wp_minchu_car_img_main" onerror="this.src=\'' . plugins_url('../img/noPhoto.png', __FILE__) . '\'">';
          }
          ?>
        <div class="wp_minchu_wall_right"></div>
        <div class="wp_minchu_wall_left"></div>
        <?php
            echo '<img src="' .plugins_url('../img/arrow_right.png',__FILE__) . '" class="wp_minchu_arrow_right">';
            echo '<img src="' .plugins_url('../img/arrow_left.png',__FILE__) . '" class="wp_minchu_arrow_left">';
           ?>
        <p class="wp_minchu_img_cmt"></p>
      </div>
      <div class="wp_minchu_thumbs">
        <div class="wp_minchu_thumbs_inner">
          <?php
            foreach($data->imgs as $img){
            ?>
          <div class="wp_minchu_thumb_img">
            <?php
            //           $errImg = 'this.src="' . get_template_directory() . '/noPhoto.png"';
             echo '<img onerror="this.src=\'' . plugins_url('../img/noPhoto.png',__FILE__) . '\'" src="' . $domain . '/car_img/' . $data->carDetail->shop->shopCd . '/' .  $data->carDetail->usedCar->usedCrId . '/' . $img->imgIdNum . '/' . $img->imgPath . '" alt="' . $img->imgCmt . '">';
            ?>
          </div>
          <?php
              }
            ?>
        </div>

      </div>
      <?php
          if($data->imgVr != null){
            $url = $domain . '/car_img/' . $data->carDetail->shop->shopCd . '/' .  $data->carDetail->usedCar->usedCrId . '/vr/' . $data->imgVr->imgPath;
            
            $json_vr = wp_remote_get($url);
            $response_code_vr = wp_remote_retrieve_response_code( $json_vr );

            if ( 200 == $response_code_vr) {
        ?>
      <section class="wp_minchu_section_vr">
        <h3 class="wp_minchu_heading">360&deg; VR画像で内観を見る</h3>
        <div class="wp_minchu_section_vr_inner">
          <div class="wp_minchu_vr_img">
            <?php
              echo '<img src="' . $domain . '/car_img/' . $data->carDetail->shop->shopCd . '/' .  $data->carDetail->usedCar->usedCrId . '/vr/' . $data->imgVr->imgPath . '">';
            ?>
            <div class="wp_minchu_mask"></div>
            <div class="wp_minchu_btn_expand"></div>
          </div>
          <p class="wp_minchu_comment">車両の内観が360&deg; VR画像にて確認できます。<br>マウスの操作によって、移動やズームも出来ますのでぜひお試しください。</p>
        </div>
      </section>
      <?php
            }
          }
        ?>
    </div>
    <div class="wp_minchu_right">
      <table class="wp_minchu_table01">
        <tr>
          <th>車両価格(税込)</th>
          <th class="wp_minchu_red">総額(税込)</th>
        </tr>
        <tr>
          <td>
            <?php 
               if($cons == "1"){
                 echo '<span class="wp_minchu_odan">応談</span>';
               }else{
                 $price = $data->carDetail->usedCar->price;
                 if($price == 0){
                   echo '0.0<span class="wp_minchu_sm">万円</span>';
                 }else{
                   echo $price . '<span class="wp_minchu_sm">万円</span>';
                 }
               }
              ?>
          </td>
          <td>
            <span class="wp_minchu_f-red">
              <?php 
                 if($cons == "1"){
                   echo "---";
                 }else{
                   $price = $data->carDetail->usedCar->totalPrice;
                   if($price == 0){
                     echo '0.0<span class="wp_minchu_sm">万円</span>';
                   }else{
                     echo $price . '<span class="wp_minchu_sm">万円</span>';
                   }
                 }
                ?>
            </span>
          </td>
        </tr>
      </table>
      <div class="wp_minchu_table02">
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">年式</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->usedCar->ageTypeCd ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">走行距離</p>
          <p class="wp_minchu_td"><?php echo number_format($data->carDetail->usedCar->mile) ?>Km</p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">車検</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->carIns ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">カラー</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->usedCar->clr ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">修復歴</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->corrctNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">燃料</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->fuelNm ?></p>
        </div>

        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">排気量</p>
          <p class="wp_minchu_td"><?php echo number_format($data->carDetail->usedCar->exhaust) ?>cc</p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">駆動</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->driveNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">ミッション</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->missionNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">ドア数</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->doorsNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">ハンドル</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->steerNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">車両</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->vehicleNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">リサイクル区分</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->rycycNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">車種</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->carTypeNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">地域</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->tdfkNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">無償保証</p>
          <p class="wp_minchu_td">
            <?php 
              $fa = $data->carDetail->usedCar->freeAssure;
              if($fa == ""){
                echo "";
              }else if($fa == "1"){
                echo "あり";
              }else{
                echo "なし";
              }
            ?>
          </p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">有償保証</p>
          <p class="wp_minchu_td">
            <?php 
              $fa = $data->carDetail->usedCar->paidAssure;
              if($fa == ""){
                echo "";
              }else if($fa == "1"){
                echo "あり";
              }else{
                echo "なし";
              }
            ?>
          </p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">法定整備</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->maintenanceNm ?></p>
        </div>
        <div class="wp_minchu_cell">
          <p class="wp_minchu_th">車両管理番号</p>
          <p class="wp_minchu_td"><?php echo $data->carDetail->usedCar->vehicleMngNum ?></p>
        </div>
      </div>

    </div>
  </section>
  <?php
      if($data->carDetail->usedCar->shopCmt != ""){
    ?>
  <section class="wp_minchu_section02 wp_minchu_mt40">
    <h3 class="wp_minchu_heading">販売店からのコメント</h3>
    <p class="wp_minchu_text"><?php echo nl2br($data->carDetail->usedCar->shopCmt); ?></p>
  </section>
  <?php
      }
    ?>
  <section class="wp_minchu_section03 wp_minchu_mt40">
    <h3 class="wp_minchu_heading wp_minchu_mb10">搭載オプション</h3>
    <ul class="wp_minchu_table03">
      <?php 
            foreach($data->optMap as $key => $value){
              $val = $value == 0 ? "false" : "true";
              echo '<li data-opt="' . $val . '">' . $key . '</li>';
            }
          ?>
    </ul>
  </section>
</div>
<div id="wp_minchu_vrPopup" class="wp_minchu_popup">
  <div class="wp_minchu_mask"></div>
  <div class="wp_minchu_popup_inner">
    <div class="wp_minchu_popup_img"></div>
    <p id="wp_minchu_info" class="wp_minchu_info">画像をタップすると回転が止まります</p>
    <button class="wp_minchu_popup_close"></button>
  </div>
  <span class="wp_minchu_btn_close"></span>
</div>

<?php 
 }
?>

<?php get_footer(); ?>
