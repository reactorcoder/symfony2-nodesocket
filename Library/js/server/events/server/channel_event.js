var event = {

	componentManager : null,

	name : 'channel_event',

	init : function () {},

	handler : function (frame) {
		if (event.actions[frame.meta.data.action]) {
			event.actions[frame.meta.data.action](frame);
		}
	},

	actions : {

		getChannelManager : function () {
			return event.componentManager.get('channel').channel;
		},

		getSubscriberManager : function () {
			return event.componentManager.get('channel').subscriber;
		},

		'save.NodeSocket\\Models\\Channel' : function (frame) {
			var channel = this.getChannelManager().create(frame.data);
		},

		'delete.NodeSocket\\Models\\Channel' : function (frame) {
			this.getChannelManager().delete(frame);
		},

		'save.NodeSocket\\Models\\Subscriber' : function (frame) {
			event.componentManager.get('channel').subscriber.create(frame.data);
		},

		'delete.NodeSocket\\Models\\Subscriber' : function (frame) {
			this.getSubscriberManager().delete(frame);
		},

		'save.NodeSocket\\Models\\SubscriberChannel' : function (frame) {
			var channel = this.getChannelManager().get(frame.data.channel_id);
			var subscriber = this.getSubscriberManager().get(frame.data.subscriber_id);
			if (channel && subscriber) {
				channel.subscribe(subscriber, frame.data, false);
			}
		},

		'delete.NodeSocket\\Models\\SubscriberChannel' : function (frame) {
			var channel = this.getChannelManager().get(frame.data.channel_id);
			var subscriber = this.getSubscriberManager().get(frame.data.subscriber_id);
			if (channel && subscriber) {
				channel.unSubscribe(subscriber);
			}
		}
	}
};

module.exports = event;