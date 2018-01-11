export default {
    methods: {
        automaticPhrase(campaign) {
            if (campaign.type !== 'automatic') {
                return '';
            }

            let timeOffset = '';
            let event = '';
            let eventValue = '';

            switch (campaign.event.schedule_type) {
                case 'hour':
                    timeOffset = `${campaign.event.schedule_offset} hour(s)`;
                    break;

                case 'day':
                    timeOffset = `${campaign.event.schedule_offset} days(s)`;
                    break;

                case 'week':
                    timeOffset = `${campaign.event.schedule_offset} weeks(s)`;
                    break;

                case 'immediately':
                default:
                    timeOffset = 'immediately';
                    break;
            }

            switch (campaign.event.action) {
                case 'wemail_subscribed_to_list':
                default:
                    event = 'someone subscribes to the list';
                    eventValue = _(weMail.lists).filter((list) => {
                        return list.id === campaign.event.value;
                    }).head().name;
                    break;
            }

            return sprintf('Automatically send an email <strong>%s</strong> after %s <strong>%s</strong>', timeOffset, event, eventValue);
        }
    }
};
