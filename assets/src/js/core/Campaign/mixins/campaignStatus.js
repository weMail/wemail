export default {
    methods: {
        campaignStatus(campaign) {
            if (!campaign) {
                campaign = this.campaign;
            }

            const type = campaign.type;
            const status = campaign.status;

            let campaignStatus = '';

            if (type === 'standard' && status === 'active' && campaign.deliver_at) {
                const deliverAt = this.toWPDateTime(campaign.deliver_at, weMail.momentDateTimeFormat);

                campaignStatus = `
                    <span class="campaign-email-status scheduled">
                        ${__('Scheduled')}
                        <span class="schedule-label">
                            <i class="dashicons dashicons-clock"></i> ${sprintf('sent at %s', deliverAt)}
                        </span>
                    </span>
                `;
            } else if (type === 'standard' && status === 'completed') {
                campaignStatus = `
                    <span class="campaign-email-status completed">
                        ${sprintf('Sent to %d subscribers', campaign.sent)}
                    </span>
                `;
            } else if (type === 'automatic' && status === 'completed') {
                campaignStatus = `
                    <span class="campaign-email-status active">
                        automatic
                    </span>
                `;
            } else {
                campaignStatus = `
                    <span class="campaign-email-status ${status}">
                        ${status}
                    </span>
                `;
            }

            return campaignStatus;
        },

        activeCampaignStatus(campaign) {
            return campaign;
        }
    }
};
