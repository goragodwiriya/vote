var modal;
function send(target, query, callback, wait, c) {
  var req = new GAjax();
  req.initLoading(wait || "wait", false, c);
  req.send(target, query, function(xhr) {
    if (callback) {
      callback.call(this, xhr);
    }
  });
}
var hideModal = function() {
  if (modal != null) {
    modal.hide();
  }
};
function showModal(src, qstr, doClose, className) {
  send(src, qstr, function(xhr) {
    var ds = xhr.responseText.toJSON();
    var detail = "";
    if (ds) {
      if (ds.alert) {
        alert(ds.alert);
      } else if (ds.detail) {
        detail = decodeURIComponent(ds.detail);
      }
    } else {
      detail = xhr.responseText;
    }
    if (detail != "") {
      modal = new GModal({
        onclose: doClose
      }).show(detail, className);
      detail.evalScript();
    }
  });
}
function defaultSubmit(ds) {
  var _alert = "",
    _input = false,
    _url = false,
    _location = false,
    t,
    el,
    remove = /remove([0-9]{0,})/;
  for (var prop in ds) {
    var val = ds[prop];
    if (prop == "error") {
      _alert = eval(val);
    } else if (prop == "debug") {
      console.log(val);
    } else if (prop == "alert") {
      _alert = val;
    } else if (prop == "modal") {
      if (modal && val == "close") {
        modal.hide();
      }
    } else if (prop == "showmodal") {
      if (!modal) {
        modal = new GModal();
      }
      modal.show(val);
      val.evalScript();
    } else if (prop == "elem") {
      el = $E(val);
      if (el) {
        el.className = ds.class;
        el.title = ds.title;
      }
    } else if (prop == "location") {
      _location = val;
    } else if (prop == "url") {
      _url = val;
      _location = val;
    } else if (prop == "open") {
      window.setTimeout(function() {
        window.open(val.replace(/&amp;/g, "&"));
      }, 1);
    } else if (remove.test(prop)) {
      if ($E(val)) {
        $G(val).fadeOut(function() {
          $G(val).remove();
        });
      }
    } else if (prop == "input") {
      el = $G(val);
      t = el.title ? el.title.strip_tags() : "";
      if (t == "" && el.placeholder) {
        t = el.placeholder.strip_tags();
      }
      if (_input != el) {
        el.invalid(t);
      }
      if (t != "" && _alert == "") {
        _alert = t;
        _input = el;
      }
    } else if ($E(prop)) {
      $G(prop).setValue(decodeURIComponent(val).replace(/\%/g, "&#37;"));
    } else if ($E(prop.replace("ret_", ""))) {
      el = $G(prop.replace("ret_", ""));
      if (el.display) {
        el = el.display;
      }
      if (val == "") {
        el.valid();
      } else {
        if (_input != el) {
          el.invalid(val);
        }
        if (_alert == "") {
          _alert = val;
          _input = el;
        }
      }
    }
  }
  if (_alert != "") {
    alert(_alert);
  }
  if (_input) {
    _input.focus();
    var tag = _input.tagName.toLowerCase();
    if (tag != "select") {
      _input.highlight();
    }
    if (tag == "input") {
      var type = _input.get("type").toLowerCase();
      if (type == "text" || type == "password") {
        _input.select();
      }
    }
  }
  if (_location) {
    if (_location == "reload") {
      window.location.reload();
    } else if (_location == "back") {
      window.history.go(-1);
    } else {
      window.location = _location.replace(/&amp;/g, "&");
    }
  }
}
function doFormSubmit(xhr) {
  var datas = xhr.responseText.toJSON();
  if (datas) {
    defaultSubmit(datas);
  } else if (xhr.responseText != "") {
    console.log(xhr.responseText);
  }
}
