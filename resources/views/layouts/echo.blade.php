<script>
    Echo.private('people.{{ Auth::user()->id }}')
        .notification((notification) => {
            if (notification.type === 'class-alert' || notification.type === 'class-message')
                showClassNotification(notification);
            else if (notification.type === 'new-class-message')
                showClassMessageNotification(notification);
        });
</script>
