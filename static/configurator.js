(function() {
    document.querySelector('form.new-short-link').addEventListener('submit', e => {
        e.preventDefault();
        let link = document.querySelector('form.new-short-link input[name=link]');
        let target = document.querySelector('form.new-short-link input[name=target]');

        if (link.value == '' || target.value == '') {
            alert('empty link or target!');
        } else {
            const api = PbAuth.apiInstance();
            api.post(SITE_LOCATION + '/pb-loader/module/link-shortener/create-link', {
                link: link.value,
                target: target.value
            }).then(res => {
                if (res.data.success) {
                    location.reload();
                } else {
                    console.log(res.data.message + ' (' + res.data.error + ')');
                    alert(res.data.message + ' (' + res.data.error + ')');
                }
            });
        }
    });

    document.querySelectorAll('a[delete-link]').forEach(link => link.addEventListener('click', e => {
        if (e.target.getAttribute('confirm') == '1') {
            const api = PbAuth.apiInstance();
            api.get(SITE_LOCATION + '/pb-loader/module/link-shortener/delete-link/' + e.target.getAttribute('delete-link')).then(res => {
                if (res.data.success) {
                    location.reload();
                } else {
                    console.log(res.data.message + ' (' + res.data.error + ')');
                    alert(res.data.message + ' (' + res.data.error + ')');
                }
            });
        } else {
            e.target.setAttribute('confirm', '1');
            e.target.innerHTML = "Confirm deletion?";
            e.target.style.color = 'red';
        }
    }));
})();