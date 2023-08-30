import tagifyMin from "@yaireo/tagify";
import "flowbite";

import jquery from "jquery";
window.jQuery = window.$ = jquery;

import select2 from "select2";
select2();

import { Ajax } from "./ajax";
import { document } from "postcss";

$(function () {
    if ($("textarea.rich-text").length > 0) {
        tinymce.init({
            selector: "textarea.rich-text",
            menubar: "",
            plugins: "link lists",
            toolbar:
                "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | numlist bullist | link | forecolor backcolor emoticons",
        });
    }

    if ($("input.tagify").length > 0) {
        let tagifyInput = document.querySelector("input.tagify");
        new tagifyMin(tagifyInput);
    }

    const selectAjax = $("select.ajax-call");
    if (selectAjax && selectAjax.length > 0) {
        selectAjax.on("change", function () {
            let apiUrl = $(this).data("route");
            let param = $(this).data("param");
            let target = $(this).data("target");
            const ajaxCall = new Ajax(
                "get",
                apiUrl + `?${param}=${$(this).val()}`
            );
            ajaxCall
                .fetch()
                .then((response) => {
                    if (response.success) {
                        let data = response.data;
                        let select = $(target);
                        let options =
                            '<option value="" selected>Please Select</option>';
                        data.forEach((item) => {
                            options += `<option value="${item.value}">${item.text}</option>`;
                        });
                        select.html(options);
                    }
                })
                .catch((error) => {
                    console.log(error.responseJSON.errors);
                });
        });
    }

    if ($("select.select2").length > 0) {
        $("select.select2").select2();
    }

    $('input[name="is_exp_required"]').on("change", function () {
        let value = parseInt($(this).val());

        if (value > 0) {
            $("#experienceBlock").removeClass("hidden");
        } else {
            $("#experienceBlock").addClass("hidden");
        }
    });

    if ($("table.action-table").length > 0) {
        $("table.action-table").on("click", ".delete-row", function () {
            let tr = $(this).closest("tr");
            let id = tr.data("id");
            if (id) {
                let dlt = tr
                    .parents("table.action-table")
                    .find("input[name='delete_items']");
                if (dlt.val() == "") {
                    dlt.val(id);
                } else {
                    let ids = dlt.val().split(",");
                    ids.push(id);
                    dlt.val(ids.join(","));
                }
            }
            tr.remove();
        });

        $("table.action-table").on("click", "button.add-row", function () {
            let table = $(this).closest("table");
            let tr = table.find("tr.form-tr").clone();
            tr.removeClass("hidden form-tr");

            let nKey = randomString(),
                inputElement = null;
            inputElement = tr.find("select, input, textarea");

            inputElement.each(function () {
                $(this).removeAttr("disabled");

                let inputName = $(this).attr("name");
                // console.log(inputName);
                inputName = inputName.replace("___", nKey).replace(' ', '');

                if (inputName) {
                    $(this).attr("name", $.trim(inputName));
                }
            });

            table.find("tr.action-add-tr").before(tr);
        });
    }

    $(document).on("change", 'input[type="file"].preview-image', function () {
        const file = this.files[0];
        const container = $(this)
            .closest(".preview-image-container")
            .find("img");
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                // console.log(event.target.result);
                container.attr("src", event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $(document).on("blur", 'input.preview-html', function () {
        const html = $(this).val();

        $(this).parents('.has-preview-html').find('.preview-html-target').html(html);
    });

    $(document).on("click", ".accordion-env", function () {
        let name = $(this).data("name");
        $(this).parents('form').find('input[name="active"]').val(name);
    });
});

const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

function randomString(length = 5) {
    let result = " ";
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(
            Math.floor(Math.random() * charactersLength)
        );
    }

    return result;
}
