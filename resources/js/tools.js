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

function showClassNotification(notification) {
    //first, set up a toast
    let template = $($('#toast-template').html());
    template.css('border-color', notification.borderColor);
    template.find('.toast-header')
        .css('background-color', notification.bgColor)
        .css('color', notification.textColor);
    template.find('.toast-icon').append($(notification.icon));
    template.find('.toast-title').html(notification.title);
    template.find('.toast-body').html(notification.message);
    $('#toast-container').append(template);
    template.toast({autohide: true, delay: 5000}).toast('show');
    //next we add the notification to the menu
    let notificationTemplate = $($('#notification-template').html());
    notificationTemplate.attr('href', notification.url)
        .css('border-color', notification.borderColor);
    notificationTemplate.find('.notification-header')
        .css('background-color', notification.bgColor)
        .css('color', notification.textColor);
    notificationTemplate.find('.notification-title')
        .html(notification.title);
    notificationTemplate.find('.notification-icon')
        .append($(notification.icon));
    notificationTemplate.find('.notification-body').html(notification.message);
    $('#notifications-dropdown-container').prepend(notificationTemplate);
    //and we make the menu visible
    $('#notification-menu').removeClass('d-none');
}

function showClassMessageNotification(notification) {
    //first, set up a toast
    let template = $($('#toast-template').html());
    template.css('border-color', notification.borderColor);
    template.find('.toast-header')
        .css('background-color', notification.bgColor)
        .css('color', notification.textColor);
    template.find('.toast-icon').append($(notification.icon));
    template.find('.toast-title').html(notification.title);
    template.find('.toast-body').html(notification.message);
    $('#toast-container').append(template);
    template.toast({autohide: true, delay: 5000}).toast('show');
}

function generatePassword()
{
    return Array(8).fill('123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz!@#$')
        .map(x => x[Math.floor(crypto.getRandomValues(new Uint32Array(1))[0] / (0xffffffff + 1) * x.length)]).join('');
}
