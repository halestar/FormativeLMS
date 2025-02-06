<script>
    Echo.private('people.{{ Auth::user()->id }}')
        .notification((notification) => {
            if(notification.type === 'class-alert')
                showClassNotification(notification);
        });
</script>
