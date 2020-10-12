$(function() {
  $('body').on('click', 'button[data-btn-type=ajax]', function(e) {
      console.log('click btn');

      var send_data;
      send_data = {
          returnOrigin : document.getElementById('id_returnOrigin').value,
          attr : document.getElementById('id_attr').value, //$('input').val()
          attr_val : document.getElementById('id_attr_val').value
      };
      console.log(send_data);
      $.ajax({
          url: 'https://sp1.local/api/attribute.php',
          dataType: "json",
          data: send_data,
          success: function(responce) {
              if (responce.result === "OK") {
                  console.log(responce);
                  $('div[data-result=""]').html(JSON.stringify(responce));
              } else {
                  HTMLFormControlsCollection.log(responce);
                  $('div[data-result=""]').html(JSON.stringify(responce));
              }
              return false;
          },

          error: function(XMLHttpReqest, textStatus, errorThrown) {
              console.log(XMLHttpReqest);
              console.log(textStatus);
              console.log(errorThrown);
              $('div[data-result=""]').html(JSON.stringify("データ取得中にエラーが発生しました。"));
              return false;
          }
      });

      $('input').focus();

      return false;
  });
});