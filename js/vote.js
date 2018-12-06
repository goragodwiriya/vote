/**
 * config.showResult
 *
 * none ไม่แสดงผล
 * always แสดงผลตลอด
 * after_vote หลังจากกด Vote
 */
function voteResult(ds) {
  // จำนวน vote ทั้งหมด และเตรียมตัวแปรสำหรับการเรียงลำดับ
  sortable = [];
  var max = 0;
  for (var i in ds) {
    ds[i].vote = floatval(ds[i].vote);
    max = Math.max(ds[i].vote, max);
    sortable.push([i, ds[i]]);
  }
  // เรียงลำดับผล vote มากไปหาน้อย
  sortable.sort(function(a, b) {
    return b[1].vote - a[1].vote;
  });
  // แสดงกราฟผลโหวต
  var dl = '<h2>ผลโหวต</h2><dl class="chart">';
  for (var i in sortable) {
    dl +=
      '<dd><img src="datas/' +
      sortable[i][1].picture +
      '" alt="' +
      sortable[i][1].name +
      '"><span class="item"><span class="label">' +
      sortable[i][1].name +
      '</span><span class="bar" style="width:' +
      (sortable[i][1].vote * 100) / max +
      '%"><span>' +
      (sortable[i][1].vote == 0 ? "&nbsp;" : sortable[i][1].vote) +
      "</span></span></span></dd>";
  }
  dl += "</dl>";
  $E("vote_result").innerHTML = dl;
}
var req = new GAjax();
var doVote = function() {
  var tel = $E("tel"),
    img = this;
  if (tel.value.length < 9) {
    alert("กรุณากรอกหมายเลขโทรศัพท์ 9 ถึง 10 หลัก");
    tel.focus();
    tel.select();
  } else if (confirm("คุณต้องการโหวตให้ " + img.alt)) {
    $G(img.parentNode.parentNode).addClass("wait");
    var q = "id=" + img.id + "&user=" + encodeURIComponent(tel.value);
    req.send("api.php/vote/post", q, function(xhr) {
      $G(img.parentNode.parentNode).removeClass("wait");
      var ds = xhr.responseText.toJSON();
      if (ds) {
        if (ds.alert) {
          alert(ds.alert);
        }
        if (ds.success) {
          tel.value = "";
        }
        if (ds.result) {
          voteResult(ds.result);
          window.scrollTo(0, $G("vote_result").getTop() - 10);
        }
      } else if (xhr.responseText != "") {
        console.log(xhr.responseText);
      }
    });
  }
};
$G(window.document).Ready(function() {
  var _scrolltop,
    toTop = $G("toTop").getTop();
  document.addEvent("scroll", function() {
    var c = this.viewport.getscrollTop() > toTop;
    if (_scrolltop != c) {
      _scrolltop = c;
      if ($E("body")) {
        if (c) {
          $E("body").className = "toTop";
        } else {
          $E("body").className = "";
        }
      } else {
        if (c) {
          document.body.addClass("toTop");
        } else {
          document.body.removeClass("toTop");
        }
      }
    }
  });
  // โหลดรายการโหวต
  req.send("./datas/people.json", null, function(xhr) {
    var ds = xhr.responseText.toJSON();
    if (ds) {
      var vote_detail = "";
      for (var i in ds.datas) {
        vote_detail +=
          '<div class="center"><figure><img id="vote_' +
          i +
          '" src="datas/' +
          ds.datas[i].picture +
          '" alt="' +
          ds.datas[i].name +
          '" title="คลิกเพื่อโหวตให้ ' +
          ds.datas[i].name +
          '"></figure><h3>' +
          ds.datas[i].name +
          "</h3></div>";
      }
      $E("vote_detail").innerHTML = vote_detail;
      forEach($E("vote_detail").querySelectorAll("img"), function() {
        callClick(this, doVote);
      });
      // โหลดผลโหวต ในครั้งแรก
      if (ds.config.showResult === "always") {
        req.send("api.php/vote/get", null, function(xhr) {
          ds = xhr.responseText.toJSON();
          if (ds) {
            voteResult(ds);
          }
        });
      }
    }
  });
  $K.init(document);
});
