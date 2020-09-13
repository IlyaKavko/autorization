$(document).ready(function () {
  // проверка на включенный js, если он отключен кнопки блокируются
  $("button").each(function () {
    $(this).prop("disabled", false);
  });

  $("form").submit(function (event) {
    event.preventDefault();
    //данная проверка очищает поля с ошибками
    $(".form-group")
      .children("#error")
      .each(function () {
        $(this).html("");
      });

    $.ajax({
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      data: new FormData(this),
      dataType: "json",
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        if (data["ERROR_EMAIL"]) {
          $(".error_email").html(data["ERROR_EMAIL_MESSAGE"]);
        } else if (data["ERROR_NAME"]) {
          $(".error_name").html(data["ERROR_NAME_MESSAGE"]);
        } else if (data["ERROR_LOGIN"]) {
          $(".error_login").html(data["ERROR_LOGIN_MESSAGE"]);
        } else if (data["ERROR_PASSWORD"]) {
          $(".error_password").html(data["ERROR_PASSWORD_MESSAGE"]);
        } else if (data["ERROR_CONFIRM_PASSWORD"]) {
          $(".error_confirm_password").html(
            data["ERROR_CONFIRM_PASSWORD_MESSAGE"]
          );
        } else if (data["ERROR_EMAIL_CHECK"]) {
          $(".error_email").html(data["ERROR_EMAIL_CHECK_MESSAGE"]);
        } else if (data["ERROR_LOGIN_CHECK"]) {
          $(".error_login").html(data["ERROR_LOGIN_CHECK_MESSAGE"]);
        } else if (data["SUCCESS"]) {
          $(".result").html(data["SUCCESS_MESSAGE"]);
          $(".result").addClass("alert alert-success");
          clearingInput();
        } else if (data["ERROR_LOGIN_NOT_FOUND"]) {
          $(".error_login_check").html(data["ERROR_LOGIN_NOT_FOUND_MESSAGE"]);
        } else if (data["ERROR_PASSWORD_NOT_FOUND"]) {
          $(".error_password_check").html(data["ERROR_PASSWORD_NOT_FOUND_MESSAGE"]);
        } else if (data["SESSION_SUCCES"]) {
          $(".row").html(data["SESSION_SUCCES_MESSAGE"]);
        }
        console.log(data);
      },
    });
  });

  // функция для очистки input
  function clearingInput() {
    $(".form-group")
      .children("input")
      .each(function () {
        $(this).val("");
      });
  }
});
