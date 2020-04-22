import route from 'ziggy'
import { Ziggy } from '../route/ziggy'

Ziggy.baseUrl = location.protocol+'//'+location.hostname;
Ziggy.baseProtocol = location.protocol;
Ziggy.baseDomain = location.hostname;

export default function (name, params, absolute) {
    return route(name, params, absolute, Ziggy)
}
