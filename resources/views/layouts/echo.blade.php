<script>
    Echo.private('people.{{ Auth::user()->id }}')
        .listen('.lmsNotification', (e) => {
            (new LmsToast(e.short_subject, e.short_body, e.action_link));
        })
        .listen('.classMessage', (e) => {
            (new LmsToast(e.short_subject, e.short_body, e.action_link, LmsToast.messageToast));
        })
</script>
