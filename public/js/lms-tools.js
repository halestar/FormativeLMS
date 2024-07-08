function confirmDelete(msg, url)
{
    if(confirm(msg))
    {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        var delInput = document.createElement('input');
        delInput.type = 'hidden';
        delInput.name = '_method';
        delInput.value = 'DELETE';
        form.appendChild(delInput);
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = jQuery('meta[name="csrf-token"]').attr('content');
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

let TextCounter = (function()
{
    function TextCounter(container, max_chars = 255, min_chars = -1)
    {
        this.container = $('#' + container);
        this.max_chars = max_chars;
        this.min_chars = min_chars;
        let text_length = this.container.val().length;
        this.count_container = $('<span class="float-end rounded count-message px-1" id="count_message">' +
            text_length + ' / ' + this.max_chars + '</span>');

        if(text_length > this.max_chars || text_length < this.min_chars)
            this.count_container.removeClass('bg-secondary').addClass('text-white bg-danger');
        else
            this.count_container.removeClass('text-white bg-danger').addClass('bg-secondary');

        this.count_container.insertAfter(this.container);

        that = this;
        this.container.on('keyup', function()
        {
            let text_length = that.container.val().length;
            if(text_length > that.max_chars || text_length < that.min_chars)
                that.count_container.removeClass('bg-secondary').addClass('text-white bg-danger');
            else
                that.count_container.removeClass('text-white bg-danger').addClass('bg-secondary');
            that.count_container.html(text_length + ' / ' + that.max_chars);
        });
    }



    return TextCounter;
})();

