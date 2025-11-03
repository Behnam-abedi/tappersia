<?php
// tappersia/admin/views/banner-types/flight-ticket/flight-ticket-preview.php

// FIX: Define YAB_PLUGIN_URL correctly for the admin preview environment
$plugin_url = defined('YAB_PLUGIN_URL') ? YAB_PLUGIN_URL : plugins_url('tappersia/') . 'tappersia/';
$svg_url = $plugin_url . 'assets/image/ticket-shape.svg';
$svg_mobile_url = $plugin_url . 'assets/image/ticket-shape-mobile.svg';
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>
    
    <div v-if="!banner.flight_ticket.from || !banner.flight_ticket.to" class="flex flex-col items-center justify-center h-80 bg-[#292929] rounded-lg p-6">
        <p class="text-gray-500 pt-[30px]">Please select an origin and destination.</p>
    </div>
    
    <transition name="fade" mode="out-in" v-else>
        
        <div v-if="currentView === 'desktop'" class="flex flex-col items-center" key="desktop">
            <span class="text-xs text-gray-400 mb-2">Desktop View</span>
            <div class="bg-[#292929] rounded-lg p-6 overflow-hidden w-full" :set="settings = banner.flight_ticket.design">
                
                <div class="promo-banner" 
                     :class="{'right-ticket-promo': settings.ticketLayout === 'right'}"
                     :style="{ 
                minHeight: settings.minHeight + 'px', 
                    borderRadius: settings.borderRadius + 'px', 
                    padding: settings.padding + 'px',
                    overflow: 'hidden',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between' /* Added */
                }">
                <div class="promo-banner__background" :style="{ 
                        background: bannerStyles(settings), 
                        zIndex: settings.layerOrder === 'overlay-below-image' ? 1 : 2,
                        borderRadius: settings.borderRadius + 'px'
                    }"></div>
                    
                    <div v-if="settings.imageUrl" class="promo-banner__image-wrapper" :style="{ 
                        zIndex: settings.layerOrder === 'overlay-below-image' ? 2 : 1, 
                        borderRadius: settings.borderRadius + 'px', 
                        overflow: 'hidden' 
                    }">
                        <img :src="settings.imageUrl" alt="Banner Image" :style="imageStyleObject(settings)">
                    </div>
                    
                    <div class="promo-banner__content" 
                         :style="{ 
                            'position': 'relative', 'zIndex': 3, 'display': 'flex', 'flexDirection': 'column', 
                            'width': settings.contentWidth + settings.contentWidthUnit, 
                            'minWidth': 0, /* 'marginLeft': '16px', */ /* Removed margin-left */
                            'justifyContent': 'center',
                            'flexGrow': 0, /* CHANGED from 1 to 0 */
                            'flexShrink': 1 
                         }">
                      <span class="promo-banner__content_1" :style="{ color: settings.content1.color, fontSize: settings.content1.fontSize + 'px', fontWeight: settings.content1.fontWeight, whiteSpace: 'normal', wordWrap: 'break-word' }">{{ settings.content1.text }}</span>
                      <span class="promo-banner__content_2" :style="{ color: settings.content2.color, fontSize: settings.content2.fontSize + 'px', fontWeight: settings.content2.fontWeight, whiteSpace: 'normal', wordWrap: 'break-word' }">{{ settings.content2.text }}</span>
                      <span class="promo-banner__content_3" :style="{ color: settings.content3.color, fontSize: settings.content3.fontSize + 'px', fontWeight: settings.content3.fontWeight, whiteSpace: 'normal', wordWrap: 'break-word' }">{{ settings.content3.text }}</span>
                    </div>
                    <div class="ticket" :style="{ 'position': 'relative', 'flex-shrink': 0 }">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 4; width: 352px; height: 129px;">
                            <img src="<?php echo esc_url($svg_url); ?>" alt="Ticket Shape Background" style="width:100%; height:100%; object-fit: contain;">
                        </div>
                        <div class="ticket__section ticket__section--actions">
                            <div class="ticket__price">
                                <div class="ticket__price-icon">
                                    <svg width="19" height="15" viewBox="0 0 19 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.60641 0.0468752C3.37203 0.171875 1.78609 1.40625 1.73922 1.49219C1.68453 1.625 1.68453 1.75 1.74703 1.86719C1.78609 1.92969 2.60641 2.52344 4.29391 3.69531C5.66891 4.64062 6.82516 5.44531 6.85641 5.47656C6.91891 5.52344 6.82516 5.58594 5.68453 6.22656C4.63766 6.8125 4.42672 6.92969 4.30172 6.92969C4.16891 6.92969 4.03609 6.85156 3.05953 6.22656C2.45797 5.84375 1.92672 5.50781 1.87984 5.47656C1.70016 5.38281 1.60641 5.42969 0.856406 5.91406C0.239219 6.3125 0.106406 6.41406 0.051719 6.52344C-0.0810935 6.78906 -0.0654685 6.80469 1.45016 8.28906C2.95797 9.75781 2.98922 9.78906 3.56734 9.9375C4.34078 10.1328 5.51266 10.0312 6.66891 9.66406C7.19234 9.5 8.62984 8.94531 9.17672 8.69531C10.5127 8.09375 14.4736 5.82031 15.9267 4.82031C17.622 3.64844 18.083 3.25 18.333 2.70312C18.6455 2.02344 18.2002 1.42188 17.2861 1.28125C16.0986 1.09375 13.6455 1.72656 11.997 2.64062L11.458 2.94531L7.66891 1.47656C5.57516 0.664063 3.83297 0 3.77828 0C3.73141 0 3.65328 0.0234377 3.60641 0.0468752ZM7.18453 2.1875C9.00484 2.89844 10.497 3.48437 10.497 3.5C10.497 3.52344 7.84078 5.02344 7.77047 5.04688C7.72359 5.0625 2.87203 1.71094 2.87203 1.66406C2.87203 1.63281 3.81734 0.90625 3.86422 0.90625C3.87203 0.898438 5.36422 1.48438 7.18453 2.1875ZM17.2783 2.13281C17.5283 2.1875 17.622 2.26562 17.5908 2.39844C17.5205 2.67187 16.5595 3.42969 14.7392 4.65625C14.1298 5.0625 10.4814 7.13281 9.43453 7.66406C8.54391 8.11719 6.83297 8.78906 6.08297 8.98437C4.95016 9.27344 3.90328 9.28906 3.43453 9.01562C3.31734 8.94531 1.12203 6.82031 1.12203 6.78125C1.12203 6.73437 1.70797 6.38281 1.75484 6.40625C1.77828 6.41406 2.23141 6.70312 2.75484 7.03906C3.27828 7.375 3.77047 7.67969 3.84859 7.71094C4.04391 7.79687 4.45797 7.8125 4.65328 7.75C4.74703 7.71875 6.58297 6.69531 8.74703 5.47656C10.9111 4.25 12.8017 3.1875 12.9502 3.11719C14.208 2.47656 16.4892 1.96094 17.2783 2.13281Z" fill="#777777"/><path d="M0.223989 13.4687C-0.057261 13.625 -0.041636 14.0547 0.255239 14.2031C0.380239 14.2656 18.0834 14.2656 18.2084 14.2031C18.4974 14.0547 18.5209 13.6406 18.2474 13.4844C18.1537 13.4297 17.4974 13.4219 9.22399 13.4219C2.14586 13.4219 0.294301 13.4297 0.223989 13.4687Z" fill="#777777"/></svg>
                                </div>
                                <div class="ticket__price-label" :style="{ fontSize: settings.price.fromFontSize + 'px' }"><span>From</span></div>
                                <div class="ticket__price-amount">
                                    <span v-if="banner.flight_ticket.isLoadingFlights" :style="{ color: settings.price.color, fontSize: settings.price.fontSize + 'px', fontWeight: settings.price.fontWeight }">...</span>
                                    <span v-else-if="banner.flight_ticket.cheapestPrice !== null" :style="{ color: settings.price.color, fontSize: settings.price.fontSize + 'px', fontWeight: settings.price.fontWeight }">
                                        €{{ banner.flight_ticket.cheapestPrice.toFixed(2) }}
                                    </span>
                                    <span v-else :style="{ color: settings.price.color, fontSize: settings.price.fontSize + 'px', fontWeight: settings.price.fontWeight }">N/A</span>
                                </div>
                            </div>
                            <div>
                                <a :href="bookingUrl" target="_blank" style="text-decoration: none;">
                                    <div class="ticket__button" 
                                         :style="{ backgroundColor: settings.button.bgColor, padding: settings.button.paddingY + 'px ' + settings.button.paddingX + 'px', borderRadius: settings.button.borderRadius + 'px', transition: 'background-color 0.3s' }"
                                         @mouseover="event.currentTarget.style.backgroundColor = settings.button.BgHoverColor || settings.button.bgColor"
                                         @mouseout="event.currentTarget.style.backgroundColor = settings.button.bgColor"
                                    >
                                        <span class="ticket__button-text" :style="{ color: settings.button.color, fontSize: settings.button.fontSize + 'px', fontWeight: settings.button.fontWeight }">Book Now</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                
                        <div class="ticket__section ticket__section--details">
                            <div class="ticket__city"><span class="ticket__city-name ticket-from-country" :style="{ color: settings.fromCity.color, fontSize: settings.fromCity.fontSize + 'px', fontWeight: settings.fromCity.fontWeight }">{{ banner.flight_ticket.from ? banner.flight_ticket.from.city : 'Origin' }}</span><div class="ticket__city-dot ticket__city-dot--origin"></div></div>
                            <div class="ticket__flight-path">
                                <div class="ticket__flight-arrow ticket__flight-arrow--top">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="7" height="20" viewBox="0 0 7 20" fill="none">
                                      <path d="M0.99 1.5L6.01 6.51" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                      <path d="M0.99 18.5V1.5" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="ticket__flight-arrow ticket__flight-arrow--bottom">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="7" height="20" viewBox="0 0 7 20" fill="none">
                                      <path d="M0.99 1.5L6.01 6.51" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                      <path d="M0.99 18.5V1.5" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ticket__city"><span class="ticket__city-name ticket-to-country" :style="{ color: settings.toCity.color, fontSize: settings.toCity.fontSize + 'px', fontWeight: settings.toCity.fontWeight }">{{ banner.flight_ticket.to ? banner.flight_ticket.to.city : 'Destination' }}</span><div class="ticket__city-dot ticket__city-dot--destination"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center" key="mobile">
            <span class="text-xs text-gray-400 mb-2">Mobile View</span>
            <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                
                <div class="promo-banner w-full" 
                     :set="settings = banner.flight_ticket.design_mobile"
                     :class="{'right-ticket-promo': banner.flight_ticket.design.ticketLayout === 'right'}"
                     :style="{ 
                minHeight: settings.minHeight + 'px', 
                    borderRadius: settings.borderRadius + 'px', 
                    padding: settings.padding + 'px',
                    overflow: 'hidden',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between' /* Added */
                }">
                <div class="promo-banner__background" :style="{ 
                        background: bannerStyles(settings), /* FIX: Changed from banner.flight_ticket.design */
                        zIndex: settings.layerOrder === 'overlay-below-image' ? 1 : 2, /* FIX: Changed from banner.flight_ticket.design */
                        borderRadius: settings.borderRadius + 'px'
                    }"></div>
                    
                    <div v-if="settings.imageUrl" class="promo-banner__image-wrapper" :style="{ /* FIX: Changed from banner.flight_ticket.design.imageUrl */
                        zIndex: settings.layerOrder === 'overlay-below-image' ? 2 : 1, /* FIX: Changed from banner.flight_ticket.design */
                        borderRadius: settings.borderRadius + 'px', 
                        overflow: 'hidden' 
                    }">
                        <img :src="settings.imageUrl" alt="Banner Image" :style="imageStyleObject(settings)"> </div>
                    
                    <div class="promo-banner__content"
                         :style="{ 
                            'position': 'relative', 'zIndex': 3, 'display': 'flex', 'flexDirection': 'column', 
                            'width': settings.contentWidth + settings.contentWidthUnit, 
                            'minWidth': 0, /* 'marginLeft': '7px', */ /* Removed margin-left */
                            'justifyContent': 'center',
                            'flexGrow': 0, /* CHANGED from 1 to 0 */
                            'flexShrink': 1
                         }">
                      <span class="promo-banner__content_1" :style="{ color: banner.flight_ticket.design.content1.color, fontSize: settings.content1.fontSize + 'px', fontWeight: settings.content1.fontWeight, whiteSpace: 'normal', wordWrap: 'break-word' }">{{ banner.flight_ticket.design.content1.text }}</span>
                      <span class="promo-banner__content_2" :style="{ color: banner.flight_ticket.design.content2.color, fontSize: settings.content2.fontSize + 'px', fontWeight: settings.content2.fontWeight, whiteSpace: 'normal', wordWrap: 'break-word' }">{{ banner.flight_ticket.design.content2.text }}</span>
                      <span class="promo-banner__content_3" :style="{ color: banner.flight_ticket.design.content3.color, fontSize: settings.content3.fontSize + 'px', fontWeight: settings.content3.fontWeight, whiteSpace: 'normal', wordWrap: 'break-word' }">{{ banner.flight_ticket.design.content3.text }}</span>
                    </div>
                    <div class="ticket mobile-ticket-preview" :style="{ 'position': 'relative', 'flex-shrink': 0 }">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 4; width: 100%; height: 100%;">
                            <img src="<?php echo esc_url($svg_mobile_url); ?>" alt="Ticket Shape Background" class="ticket-shape" style="width:100%; height:100%; object-fit: contain;">
                            </div>
                        <div class="ticket__section ticket__section--actions">
                            <div class="ticket__price">
                                <div class="ticket__price-icon">
                                    <svg width="10" height="10" viewBox="0 0 19 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.60641 0.0468752C3.37203 0.171875 1.78609 1.40625 1.73922 1.49219C1.68453 1.625 1.68453 1.75 1.74703 1.86719C1.78609 1.92969 2.60641 2.52344 4.29391 3.69531C5.66891 4.64062 6.82516 5.44531 6.85641 5.47656C6.91891 5.52344 6.82516 5.58594 5.68453 6.22656C4.63766 6.8125 4.42672 6.92969 4.30172 6.92969C4.16891 6.92969 4.03609 6.85156 3.05953 6.22656C2.45797 5.84375 1.92672 5.50781 1.87984 5.47656C1.70016 5.38281 1.60641 5.42969 0.856406 5.91406C0.239219 6.3125 0.106406 6.41406 0.051719 6.52344C-0.0810935 6.78906 -0.0654685 6.80469 1.45016 8.28906C2.95797 9.75781 2.98922 9.78906 3.56734 9.9375C4.34078 10.1328 5.51266 10.0312 6.66891 9.66406C7.19234 9.5 8.62984 8.94531 9.17672 8.69531C10.5127 8.09375 14.4736 5.82031 15.9267 4.82031C17.622 3.64844 18.083 3.25 18.333 2.70312C18.6455 2.02344 18.2002 1.42188 17.2861 1.28125C16.0986 1.09375 13.6455 1.72656 11.997 2.64062L11.458 2.94531L7.66891 1.47656C5.57516 0.664063 3.83297 0 3.77828 0C3.73141 0 3.65328 0.0234377 3.60641 0.0468752ZM7.18453 2.1875C9.00484 2.89844 10.497 3.48437 10.497 3.5C10.497 3.52344 7.84078 5.02344 7.77047 5.04688C7.72359 5.0625 2.87203 1.71094 2.87203 1.66406C2.87203 1.63281 3.81734 0.90625 3.86422 0.90625C3.87203 0.898438 5.36422 1.48438 7.18453 2.1875ZM17.2783 2.13281C17.5283 2.1875 17.622 2.26562 17.5908 2.39844C17.5205 2.67187 16.5595 3.42969 14.7392 4.65625C14.1298 5.0625 10.4814 7.13281 9.43453 7.66406C8.54391 8.11719 6.83297 8.78906 6.08297 8.98437C4.95016 9.27344 3.90328 9.28906 3.43453 9.01562C3.31734 8.94531 1.12203 6.82031 1.12203 6.78125C1.12203 6.73437 1.70797 6.38281 1.75484 6.40625C1.77828 6.41406 2.23141 6.70312 2.75484 7.03906C3.27828 7.375 3.77047 7.67969 3.84859 7.71094C4.04391 7.79687 4.45797 7.8125 4.65328 7.75C4.74703 7.71875 6.58297 6.69531 8.74703 5.47656C10.9111 4.25 12.8017 3.1875 12.9502 3.11719C14.208 2.47656 16.4892 1.96094 17.2783 2.13281Z" fill="#777777"/><path d="M0.223989 13.4687C-0.057261 13.625 -0.041636 14.0547 0.255239 14.2031C0.380239 14.2656 18.0834 14.2656 18.2084 14.2031C18.4974 14.0547 18.5209 13.6406 18.2474 13.4844C18.1537 13.4297 17.4974 13.4219 9.22399 13.4219C2.14586 13.4219 0.294301 13.4297 0.223989 13.4687Z" fill="#777777"/></svg>
                                </div>
                                <div class="ticket__price-label" :style="{ fontSize: settings.price.fromFontSize + 'px' }"><span>From</span></div>
                                <div class="ticket__price-amount">
                                    <span v-if="banner.flight_ticket.isLoadingFlights" :style="{ color: banner.flight_ticket.design.price.color, fontSize: settings.price.fontSize + 'px', fontWeight: settings.price.fontWeight }">...</span>
                                    <span v-else-if="banner.flight_ticket.cheapestPrice !== null" :style="{ color: banner.flight_ticket.design.price.color, fontSize: settings.price.fontSize + 'px', fontWeight: settings.price.fontWeight }">
                                        €{{ banner.flight_ticket.cheapestPrice.toFixed(2) }}
                                    </span>
                                    <span v-else :style="{ color: banner.flight_ticket.design.price.color, fontSize: settings.price.fontSize + 'px', fontWeight: settings.price.fontWeight }">N/A</span>
                                </div>
                            </div>
                            <div>
                                <a :href="bookingUrl" target="_blank" style="text-decoration: none;">
                                    <div class="ticket__button" 
                                         :style="{ backgroundColor: banner.flight_ticket.design.button.bgColor, padding: settings.button.paddingY + 'px ' + settings.button.paddingX + 'px', borderRadius: settings.button.borderRadius + 'px', transition: 'background-color 0.3s' }"
                                         @mouseover="event.currentTarget.style.backgroundColor = banner.flight_ticket.design.button.BgHoverColor || banner.flight_ticket.design.button.bgColor"
                                         @mouseout="event.currentTarget.style.backgroundColor = banner.flight_ticket.design.button.bgColor"
                                    >
                                        <span class="ticket__button-text" :style="{ color: banner.flight_ticket.design.button.color, fontSize: settings.button.fontSize + 'px', fontWeight: settings.button.fontWeight }">Book Now</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                
                        <div class="ticket__section ticket__section--details">
                            <div class="ticket__city"><span class="ticket__city-name ticket-from-country" :style="{ color: banner.flight_ticket.design.fromCity.color, fontSize: settings.fromCity.fontSize + 'px', fontWeight: settings.fromCity.fontWeight }">{{ banner.flight_ticket.from ? banner.flight_ticket.from.city : 'Origin' }}</span><div class="ticket__city-dot ticket__city-dot--origin"></div></div>
                            <div class="ticket__flight-path">
                                <div class="ticket__flight-arrow ticket__flight-arrow--top">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="7" height="20" viewBox="0 0 7 20" fill="none">
                                      <path d="M0.99 1.5L6.01 6.51" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                      <path d="M0.99 18.5V1.5" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="ticket__flight-arrow ticket__flight-arrow--bottom">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="7" height="20" viewBox="0 0 7 20" fill="none">
                                      <path d="M0.99 1.5L6.01 6.51" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                      <path d="M0.99 18.5V1.5" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ticket__city"><span class="ticket__city-name ticket-to-country" :style="{ color: banner.flight_ticket.design.toCity.color, fontSize: settings.toCity.fontSize + 'px', fontWeight: settings.toCity.fontWeight }">{{ banner.flight_ticket.to ? banner.flight_ticket.to.city : 'Destination' }}</span><div class="ticket__city-dot ticket__city-dot--destination"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </transition>
    </div>