const store = new Vuex.Store({
    modules: {
        global: {
            namespaced: true,

            state: {
                i18n: weMail.i18n,
                lists: weMail.lists,
                lifeStages: weMail.lifeStages
            },

            mutations: {
                addList(state, list) {
                    const lists = state.lists.concat(list);
                    state.lists = _.orderBy(lists, 'name', 'asc');
                },

                updateList(state, list) {
                    const lists = state.lists;

                    const listInState = _.find(lists, {
                        id: list.id
                    });

                    listInState.name = list.name;
                    listInState.type = list.type;

                    state.lists = _.orderBy(lists, 'name', 'asc');
                },

                removeList(state, listId) {
                    const listIndex = _.findIndex(state.lists, {
                        id: listId
                    });

                    state.lists.splice(listIndex, 1);
                }
            }
        }
    }
});

export default store;
