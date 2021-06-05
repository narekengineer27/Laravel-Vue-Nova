import {} from './bsumm'
Nova.booting((Vue, router) => {
    Vue.component('MapBox', require('./components/Card'));
    Vue.component('MapBoxDetail', require('./components/Detail'));
})
