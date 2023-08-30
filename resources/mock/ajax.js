export class Ajax {
    constructor(type, url, data) {
        this.type = type;
        this.url = url;
        this.data = data;
    }

    fetch() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: this.type,
                url: this.url,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: this.data,
                success: function (response) {
                    resolve(response);
                },
                error: function (error) {
                    reject(error);
                },
            });
        });
    }
}
