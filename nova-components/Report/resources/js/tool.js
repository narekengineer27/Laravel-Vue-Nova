Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'report',
            path: '/report',
            component: require('./components/Tool'),
            meta: { label: 'Top Categories' }
        },
        {
            name: 'top',
            path: '/report',
            component: require('./components/Tool'),
            meta: { label: 'Top Categories' }
        },
    ])
})
