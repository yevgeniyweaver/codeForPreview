<template>
    <!-- BEGIN CONTAINER -->
    <div class="page-container">

        <div v-show="isBusy" class="vue-bg-spinner-loader">
            <div class="spinner-loader">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            </div>
        </div>

        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <!-- BEGIN PAGE HEAD-->
            <div class="page-head">
                <div class="container-fluid">
                    <!-- BEGIN PAGE TITLE -->
                    <div class="page-title">
                        <h1>DSP</h1>
                    </div>
                    <!-- END PAGE TITLE -->
                </div>
            </div>
            <!-- END PAGE HEAD-->
            <!-- BEGIN PAGE CONTENT BODY -->
            <div class="page-content">
                <div class="container-fluid">
                    <!-- BEGIN PAGE CONTENT INNER -->
                    <div class="page-content-inner">

                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="portlet light ">
                                    <div class="portlet-title">
                                        <div class="form-inline dsp-list-form">

                                            <b-form-input
                                                ref="filterInput"
                                                v-model="filterValue"
                                                @input="searchFilter()"
                                                v-on:keyup.enter="openEndpointEditLink"
                                                :placeholder="filterPlaceholder"
                                                class="form-control filterInput"
                                            ></b-form-input>

                                            <div class="form-group">
                                                <div class="input-group">
                                                    <ex-select v-model="statusTxt"
                                                               :options="statusesTxt"
                                                               :searchable="false"
                                                               @input="changeStatusTxt"
                                                               class="input-small"
                                                               placeholder="(Status)"
                                                    />
                                                </div>
                                            </div>

                                            <router-link v-if="isManager"
                                                         :to="{name:'dsp-create'}"
                                                         class="btn blue"
                                            >Create Endpoint</router-link>

                                            <div v-if="isManager && listManagers.length && ['SmartyAds','GothamAds','BizzClick','Aceex','Aceex2'].includes(project)"
                                                 class="form-group">
                                                <div class="input-group">
                                                    <ex-select v-model="selectedManager"
                                                               :options="listManagers"
                                                               :searchable="false"
                                                               @input="changeManager"
                                                               class="input-medium"
                                                               placeholder="(Manager)"
                                                    />
                                                </div>
                                            </div>

                                            <div v-if="isManager && regions.length && ['SmartyAds','GothamAds','BizzClick','Aceex','Aceex2'].includes(project)"
                                                 class="form-group">
                                                <div class="input-group">
                                                    <ex-select v-model="selectedRegion"
                                                               :options="regions"
                                                               :searchable="false"
                                                               @input="changeRegion"
                                                               class="input-small"
                                                               placeholder="(Region)"
                                                    />
                                                </div>
                                            </div>

                                            <router-link :to="{name:'dsp-details'}"
                                                         class="btn blue f-r"
                                            >Details of settings</router-link>

                                            <a v-if="['Acuity', 'SmartyAds', 'Integral Stream'].includes(project)"
                                               href="/dsp/list"
                                               class="btn blue f-r"
                                               style="margin-right: 5px"
                                            ><i class="fa fa-refresh" aria-hidden="true"></i> Endpoints</a>

                                            <button @click="exportToFile('xlsx')" type="button" class="btn green f-r m-rl-5px">Download</button>

                                            <div class="inline-block">
                                                <span style="color: #3f74a3;"> Colors tooltip: </span>
                                                <div class="inline-block v-alig-m"
                                                     style="width: 30px; height: 30px; border: 1px solid #b7b7b7; background:#dbf7f9;"
                                                     title="No bid"
                                                ></div>
                                                <div class="inline-block v-alig-m"
                                                     style="width: 30px; height: 30px; border: 1px solid #b7b7b7; background:#fffae9;"
                                                     title="Spend cap reached yesterday"
                                                ></div>
                                                <div v-if="['SmartyAds','GothamAds','BizzClick'].includes(project)"
                                                     class="inline-block v-alig-m"
                                                     style="width: 30px; height: 30px; border: 1px solid #b7b7b7; background:#fbe1e3;"
                                                     title="Not approved"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body">

                                        <div class="table-scrollable">
                                            <b-table striped
                                                         :sticky-header="stickyHeaderHeight + 'px'"
                                                         ref="dspIndexTable"
                                                         :fields="fields"
                                                         :items="items"
                                                         :busy="isBusy"
                                                         :show-empty="true"
                                                         empty-text="No data to display"
                                                         @sort-changed="setSortOptions"
                                                         :sort-direction="sortDesc ? 'desc' : 'asc'"
                                                         :sort-by="sortBy"
                                                         :sort-desc="sortDesc"
                                                         :no-sort-reset="true"
                                                         :tbody-tr-class="parentRowClass"
                                                         @row-clicked="item=>$set(item, '_showDetails', !item._showDetails)"
                                                         :current-page="pagination.currentPage"
                                                         :per-page="0"
                                                >
                                                    <template v-slot:cell(id)="data">
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </template>

                                                    <template #head(bid_request)="data">
                                                        Bid Request <br>
                                                        Example
                                                    </template>

                                                    <template #head(bid_response)="data">
                                                        Bid Response <br>
                                                        Example
                                                    </template>

                                                    <template v-if="isManager" v-slot:cell(keyname)="data">
                                                        <router-link :to="{ name:'dsp-edit', params:{ id:data.item.id } }"
                                                        >{{ data.value }}</router-link>
                                                    </template>

                                                    <template v-slot:cell(yesterdayspend)="data">
                                                        <span>
                                                            $ {{ data.value.toFixed(2) }}
                                                        </span>
                                                    </template>

                                                    <template v-slot:cell(dailyspend)="data">
                                                        <span>
                                                            $ {{ data.value.toFixed(2) }}
                                                        </span>
                                                    </template>
                                                    <template v-slot:cell(spendlimit)="data">
                                                            <span>
                                                                $ {{ Number(data.value).toFixed(0) }}
                                                            </span>
                                                    </template>
                                                    <template v-slot:cell(winrate)="data">
                                                        <span>
                                                         {{ data.item.winrate ? Number(data.item.winrate).toFixed(2) : 0 }} %
                                                        </span>
                                                    </template>

                                                    <template v-slot:cell(bid_request)="data">
                                                        <a v-if="data.item.id" :href="'/dsp/bidrequest/id/' + data.item.id" target="_blank">
                                                            show
                                                        </a>
                                                    </template>

                                                    <template v-slot:cell(bid_response)="data">
                                                        <a v-if="data.item.id" :href="'/dsp/bidresponse/id/' + data.item.id" target="_blank">
                                                            show
                                                        </a>
                                                    </template>
                                                    <template v-slot:cell(adaptraffic)="data">
                                                        <b-form-checkbox
                                                            :checked="data.item.adaptraffic > 0"
                                                            :disabled="true"
                                                            :indeterminate="data.item.adaptraffic > 0 && data.item.adaptraffic < 1"
                                                        />
                                                    </template>
                                                    <template #row-details="data" class="m-blank-0">
                                                        <b-table striped
                                                                 ref="dspEpTable"
                                                                 class="light-table no-border"
                                                                 :fields="fields"
                                                                 :items="data.item.endpoints"
                                                                 :tbody-tr-class="rowClass"
                                                                 thead-class="hidden_header"
                                                        >
                                                            <template v-slot:cell(company_name)="data">
                                                                <div v-if="isManager">
                                                                    <router-link :to="{ name:'dsp-edit', params:{ id:data.item.id } }"
                                                                    >{{ data.item.keyname }}</router-link>
                                                                </div>
                                                                <div v-else>{{ data.value }}</div>
                                                            </template>

                                                            <template v-slot:cell(endpoint)="data">
                                                                <a @click="inputSetEndpoint = data.value"
                                                                   href="javascript:void(0)"
                                                                   :id="'popoverSetEndpoint_' + data.item.id">
                                                                    <span>{{ sliceText(data.value) }}</span>
                                                                </a>
                                                                <b-popover :target="'popoverSetEndpoint_' + data.item.id"
                                                                           triggers="click blur"
                                                                           :ref="'popoverSetEndpoint_' + data.item.id"
                                                                           :id="'popoverSetEndpointShow_' + data.item.id"
                                                                           placement="bottom"
                                                                           style="width:220px;">
                                                                    <b-form-input v-if="isManager"
                                                                                  v-model="inputSetEndpoint"
                                                                                  placeholder="Enter endpoint"
                                                                                  style="width: 72%; float:left">
                                                                    </b-form-input>
                                                                    <span v-else style="word-break: break-all;">
                                                                    {{ inputSetEndpoint }}
                                                                </span>
                                                                    <button v-if="isManager"
                                                                            @click="changeEndpoint(data.item)"
                                                                            type="button"
                                                                            class="btn blue f-r">
                                                                        Save
                                                                    </button>
                                                                </b-popover>
                                                            </template>
                                                            <template v-if="isManager" v-slot:cell(spendlimit)="data">
                                                                <div v-if="data.item.spendlimit">
                                                                    <a href="javascript:void(0)" :id="'popoverSetSpendLimit_'+data.item.id"
                                                                       @click="inputSetSpendLimit = data.value"
                                                                    >${{ data.value.toFixed(0) }}</a>
                                                                    <b-popover :target="'popoverSetSpendLimit_'+data.item.id"
                                                                               triggers="click blur"
                                                                               :ref="'popoverSetSpendLimit_'+data.item.id"
                                                                               :id="'popoverSetSpendLimitShow_'+data.item.id"
                                                                               placement="left"
                                                                               style="width:220px"
                                                                    >
                                                                        <b-form-input v-model="inputSetSpendLimit"
                                                                                      placeholder="Enter spend limit"
                                                                                      style="width: 72%; float:left"
                                                                        ></b-form-input>
                                                                        <button type="button" class="btn blue f-r"
                                                                                @click="changeSpendLimit(data.item)"
                                                                        > Save </button>
                                                                    </b-popover>
                                                                </div>
                                                                <div v-else>
                                                                    <span>{{ data.item.company_name }}</span>
                                                                </div>
                                                            </template>
                                                            <template v-slot:cell(winrate)="data">
                                                                <span v-if="winRate[data.item.keyname]">
                                                                 {{ Number(winRate[data.item.keyname].winRate).toFixed(2) }} %
                                                                </span>
                                                                <span v-else> 0 </span>
                                                            </template>
                                                            <template v-slot:cell(bid_request)="data">
                                                                <a :href="'/dsp/bidrequest/id/' + data.item.id" target="_blank">
                                                                    show
                                                                </a>
                                                            </template>
                                                            <template v-slot:cell(bid_response)="data">
                                                                <a :href="'/dsp/bidresponse/id/' + data.item.id" target="_blank">
                                                                    show
                                                                </a>
                                                            </template>
                                                            <template v-slot:cell(comments)="data">
                                                                <span v-if="isManager">
                                                                    <a href="javascript:void(0)" :id="'popoverSetComment_'+data.item.id"
                                                                       @click="inputSetComment = data.value"
                                                                    >
                                                                        <span v-if="data.value" v-b-tooltip.hover :title="data.value"> {{ sliceText(data.value) }}</span>
                                                                        <span v-else class="empty-comment" v-b-tooltip.hover title="Add comment"> Add comment </span>
                                                                    </a>
                                                                    <b-popover :target="'popoverSetComment_'+data.item.id"
                                                                               triggers="click blur"
                                                                               :id="'popoverSetCommentShow_'+data.item.id"
                                                                               :ref="'popoverSetComment_'+data.item.id"
                                                                               placement="left"
                                                                               style="width:220px">
                                                                        <b-form-textarea v-model="inputSetComment"
                                                                                         placeholder="Enter comments"
                                                                                         style="width: 72%; float:left"
                                                                                         rows="3"
                                                                        ></b-form-textarea>
                                                                        <button type="button" class="btn blue f-r"
                                                                                @click="changeComment(data.item)"
                                                                        > Save </button>
                                                                    </b-popover>
                                                                </span>
                                                                <span v-else>
                                                                {{ sliceText(data.value) }}
                                                            </span>
                                                            </template>
                                                            <template v-slot:cell(qps)="data">
                                                                <span v-if="isManager">
                                                                    <a @click="inputSetLimitQps = data.value"
                                                                       href="javascript:void(0)"
                                                                       :id="'popoverSetLimitQps_'+data.item.id">
                                                                        {{ data.value }}
                                                                    </a>
                                                                    <b-popover :target="'popoverSetLimitQps_'+data.item.id"
                                                                               triggers="click blur"
                                                                               :ref="'popoverSetLimitQps_'+data.item.id"
                                                                               :id="'popoverSetLimitQpsShow_'+data.item.id"
                                                                               placement="bottom"
                                                                               style="width:220px">
                                                                        <b-form-input v-model="inputSetLimitQps"
                                                                                      placeholder="Enter QPS"
                                                                                      style="width: 72%; float:left">
                                                                        </b-form-input>
                                                                        <button @click="changeLimitQps(data.item)"
                                                                                type="button"
                                                                                class="btn blue f-r">
                                                                            Save
                                                                        </button>
                                                                    </b-popover>
                                                                </span>
                                                                <span v-else> {{ data.value }} </span>
                                                            </template>
                                                            <template v-slot:cell(apilink)="data">
                                                                <a v-if="data.item.keyname" :href="prepareApiUrl(data.item)" target="_blank"> Link </a>
                                                            </template>
                                                            <template v-if="isManager" v-slot:cell(action)="data">
                                                                <div v-if="data.item.active === -1">
                                                                    <a href="javascript:void(0);"
                                                                       @click="restoreEndpoint(data.item)"
                                                                       class="icon restoreEndpoint"
                                                                    >
                                                                        <i class="fa fa-undo"></i>
                                                                    </a>
                                                                </div>
                                                                <div v-else>
                                                                    <a v-if="isFinance && data.item.active === 0"
                                                                       href="javascript:void(0);"
                                                                       @click="approveEndpoint(data.item)"
                                                                       class="icon approveEndpoint"
                                                                    >
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                    <a v-else
                                                                       href="javascript:void(0);"
                                                                       @click="archiveEndpoint(data.item)"
                                                                       class="icon archiveEndpoint"
                                                                    >
                                                                        <i class="fa fa-archive"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)"
                                                                       @click="deleteEndpoint(data.item)"
                                                                       class="icon delEndpoint"
                                                                    >
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                </div>
                                                            </template>
                                                            <template v-slot:cell(yesterdayspend)="data">
                                                                <span>
                                                                    $ {{ data.value.toFixed(2) }}
                                                                </span>
                                                            </template>
                                                            <template v-slot:cell(dailyspend)="data">
                                                                <span>
                                                                    $ {{ data.value.toFixed(2) }}
                                                                </span>
                                                            </template>
                                                            <template v-slot:cell(adaptraffic)="data">
                                                                <b-form-checkbox
                                                                    :checked="!!data.item.adaptraffic"
                                                                    :disabled="true"
                                                                />
                                                            </template>
                                                        </b-table>
                                                    </template>

                                            </b-table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PAGE CONTENT INNER -->
                </div>
            </div>
            <!-- END PAGE CONTENT BODY -->
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->


    </div>
    <!-- END CONTAINER -->
</template>

<script>
import axios from 'axios';
import r from '../../route';
import moment from 'moment';
import {debounce} from "debounce";
import { PerfectScrollbar } from "vue2-perfect-scrollbar";

export default {
    name: "DspCompanyList",
    components: {
    },
    data() {
        return {
            project: process.env.MIX_APP_NAME,
            isBusy: false,
            userRole: '',
            isManager: false,
            isFinance: false,
            exportShow: false,
            toggleEndpoints: false,
            stickyHeaderHeight: 380,
            statusTxt: null,
            statusesTxt: [
                {label: 'Pending', value: 'view_pending'},
                {label: 'Active', value: 'view_active'},
                {label: 'Not Active', value: 'view_notactive'},
                {label: 'Archived', value: 'view_archived'},
            ],
            statusIntegration: null,
            statusesIntegration: [
                {label: 'Open RTB', value: 'integration_rtb'},
            ],
            selectedManager: null,
            listManagers: [],
            selectedRegion: null,
            regions: [],
            fields: [
                {key: 'id', label: 'ID', sortable: false, width: 90, stickyColumn: true, class: 'td-id', sortDirection: 'desc'},
                {key: 'company_name', label: 'Name', sortable: true, stickyColumn: true, class: 'td-companyname', sortDirection: 'desc'},
                {key: 'endpoint', label: 'Endpoint', sortable: false, sortDirection: 'desc', class: 'td-endpoint'},
                {key: 'usebanner', label: 'B', sortable: true, sortDirection: 'desc', class: 'td-short'},
                {key: 'usenative', label: 'N', sortable: true, sortDirection: 'desc', class: 'td-short'},
                {key: 'usevideo', label: 'V', sortable: true, sortDirection: 'desc', class: 'td-short'},

                {key: 'adaptraffic', label: 'Adp', sortable: true, sortDirection: 'desc', class: 'td-medium'},
                {key: 'region', label: 'Region', sortable: true, sortDirection: 'desc', class: 'td-region'},

                {key: 'qps', label: 'Limit QPS', sortable: true, sortDirection: 'desc', class: 'td-bid'},
                {key: 'real_qps', label: 'Real QPS', sortable: true, sortDirection: 'desc', class: 'td-bid'},
                {key: 'bid_qps', label: 'BPS', sortable: true, sortDirection: 'desc', class: 'td-bid', class: 'td-medium'},
                {key: 'yesterdayspend', label: 'Spend Yesterday', sortable: true, sortDirection: 'desc', class: 'td-spend'},
                {key: 'dailyspend', label: 'Spend Today', sortable: true, sortDirection: 'desc', class: 'td-spend'},


                {key: 'winrate', label: 'Win Rate', sortable: true, sortDirection: 'desc'},
                {key: 'bid_request', label: '', sortable: false, class: 'td-bid'},
                {key: 'bid_response', label: '', sortable: false, class: 'td-bid'},
                {key: 'spendlimit', label: 'Spend limit', sortable: true, sortDirection: 'desc', class: 'td-spend'},
                {key: 'comments', label: 'Comments', sortable: false, sortDirection: 'desc', class: 'td-endpoint'},
                {key: 'apilink', label: 'API Link', sortable: false, class: 'td-medium'},
                {key: 'action', label: 'Action', sortable: false, class: 'td-medium'},
            ],
            filters: {
                id: '',
                keyname: '',
                company_name: ''
            },
            filterValue: '',
            filterPlaceholder: 'ID, endpoint or company',
            items: [],
            currentEndpointstems: [],
            sortBy: 'company_name',
            sortDesc: false,
            activeRequests: 0,
            pagination: {
                //currentPage: 1,
                currentPage: 0,
                totalPages: 1,
                endResult: false
            },
            winRate: [],
            inputSetEndpoint: null,
            inputSetLimitQps: null,
            inputSetSpendLimit: null,
            inputSetComment: null,
            dateFrom: null,
            dateTo: null,
            stickyHead: {
                top: 0,
                headRef: null
            },
        }
    },
    watch: {
        isBusy: function(currentValue, oldValue) {
            if(!currentValue) {
                this.stickyHead.top = this.stickyHead.headRef.getBoundingClientRect().top;
                this.handleScrollForHead();
            }
        }
    },
    created() {
        if (['SmartyAds', 'BizzClick'].includes(this.project)) {
            this.statusesIntegration.splice(2, 0, {value: 'integration_prebid', label: 'Prebid'});
        }
        if (['SmartyAds'].includes(this.project)) {
            this.statusesIntegration.splice(3, 0, {value: 'integration_xml', label: 'XML'});
        }
        if (['SmartyAds', 'GothamAds', 'BizzClick', 'Aceex'].includes(this.project)) {
            this.statusesIntegration.splice(4, 0, {value: 'integration_vast', label: 'VAST'});
        }
        if (['GothamAds', 'BizzClick'].includes(this.project)) {
            this.statusesIntegration.splice(5, 0, {value: 'integration_pub', label: 'DirectPub'});
        }
        if (['SmartyAds', 'GothamAds', 'BizzClick', 'Aceex', 'Aceex2'].includes(this.project)) {
            this.fields.splice(6, 0, {key: 'useaudio', label: 'A', sortable: true, sortDirection: 'desc', class: 'td-short'});
        }
        if (['SmartyAds', 'GothamAds', 'BizzClick'].includes(this.project)) {
            this.fields.splice(7, 0, {key: 'usepop', label: 'P', sortable: true, sortDirection: 'desc', class: 'td-short'});
        }
        if (['Aceex', 'Aceex2'].includes(this.project)) {
            this.fields.splice(8, 0, {key: 'maxqps', label: 'Max QPS', sortable: true, sortDirection: 'desc'});
        }
        if (['Acuity'].includes(this.project)) {
            this.fields.splice(8, 0, {key: 'actmarga', label: 'Fl.Margin (%) /  Act.Margin (%)', sortable: true, sortDirection: 'desc'});
        }
        window.addEventListener('scroll', this.handleScrollForLoad);
        window.addEventListener('scroll', this.handleScrollForHead);
        this.getDspCompanyList();
    },
    mounted() {
        this.setStickyHeaderMaxHeight();
        this.stickyHead.headRef = Array.from(document.querySelectorAll('thead')).pop();
        this.stickyHead.top = this.stickyHead.headRef.getBoundingClientRect().top;
        document.addEventListener("keydown", this.actionCtrlF);
        window.addEventListener('scroll', this.handleScrollForHead);  
        window.addEventListener('resize', this.initStickyHead);
    },
    beforeDestroy() {
        document.removeEventListener("keydown", this.actionCtrlF);
        window.removeEventListener('scroll', this.handleScrollForLoad);
        window.removeEventListener('scroll', this.handleScrollForHead);
    },
    methods: {
        initStickyHead() {
            this.stickyHead.headRef = Array.from(document.querySelectorAll('thead')).pop();
            this.stickyHead.top = this.stickyHead.headRef.getBoundingClientRect().top;
            console.log(this.stickyHead.headRef.getBoundingClientRect().top);
            this.handleScrollForHead();
        },
        openEndpointEditLink() {
            if (this.items.length > 1) {
                return;
            }
            if (this.items[0].endpoints.length > 1) {
                return;
            }
            let route = this.$router.resolve({ path: `/dsp/edit/id/${this.items[0].endpoints[0].id}` });
            window.open(route.href);
        },
        searchFilter: debounce(function () {
            this.resetTableData();
            this.$refs.dspIndexTable.refresh();
        }, 700),
        actionCtrlF(e) {
            if (e.keyCode === 114 || (e.ctrlKey && e.keyCode === 70)) {
                e.preventDefault();
                this.$refs.filterInput.focus();
                this.stickyHead.headRef.style.top = '0';
            }
        },
        setFilterData() {
            let filterData = {
                keyname: this.filterValue,
                company_name: this.filterValue
            };
            const filterValNumber = Number(this.filterValue);
            if(this.filterValue && !isNaN(filterValNumber)) {
                filterData.id = filterValNumber;
            }
            return filterData;
        },
        handleScrollForLoad() {
            if (this.isBusy) {
                return;
            }
            if((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                this.getDspCompanyList();
            }
        },
        handleScrollForHead() {
            if (window.scrollY >= this.stickyHead.top) {
                const heightDiff = window.scrollY - this.stickyHead.top;
                if (heightDiff > 0) {
                    this.stickyHead.headRef.style.top = heightDiff + 'px';
                } else {
                    this.stickyHead.headRef.style.top = '0';
                }
            } else {
                this.stickyHead.headRef.style.top = '';
            }
        },
        resetTableData() {
            this.pagination.currentPage = 0;
            this.pagination.endResult = false;
            this.items = [];
            this.getDspCompanyList();
        },
        setSortOptions(options) {
            this.sortDesc = options.sortBy === this.sortBy ? !this.sortDesc : true;
            this.sortBy = options.sortBy;
            this.resetTableData();
        },
        prepareListOptions(data, key, value) {
            let listOptions = [];
            Object.values(data).forEach((option) => {
                listOptions.push({code: option[key], label: option[value]})
            })
            return listOptions;
        },
        changeStatusTxt() {
            this.isBusy = true;
            axios.post('/cookie/set', {
                'cookieName': 'tbl_dsp_status',
                'cookieVal': this.statusTxt ? this.statusTxt.value : '',
            }).then(response => {
                this.isBusy = false;
                let table = this.$refs.dspIndexTable;
                this.resetTableData();
                Vue.nextTick(function () {
                    table.refresh();
                });
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        changeManager() {
            this.isBusy = true;
            axios.post('/cookie/set', {
                'cookieName': 'activeManagerDsp',
                'cookieVal': this.selectedManager ? this.selectedManager.code : '',
            }).then(response => {
                this.isBusy = false;
                let table = this.$refs.dspIndexTable;
                this.resetTableData();
                Vue.nextTick(function () {
                    table.refresh();
                });
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        changeRegion() {
            this.isBusy = true;
            axios.post('/cookie/set', {
                'cookieName': 'selectedRegionDsp',
                'cookieVal': this.selectedRegion ? this.selectedRegion : '',
            }).then(response => {
                this.isBusy = false;
                this.pagination.currentPage = 1;
                let table = this.$refs.dspIndexTable;
                this.resetTableData();
                Vue.nextTick(function () {
                    table.refresh();
                });
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        sliceText(text) {
            return text.slice(0, 23) + (text.length > 23 ? '...' : '');
        },
        getDspCompanyList(ctx) {
            if (this.isBusy || !this.sortBy || this.pagination.endResult || this.activeRequests) {
                return false;
            }

            this.isBusy = true;
            this.pagination.currentPage += 1;

            const filterData = this.setFilterData();

            const params = new URLSearchParams();
            params.append('page', this.pagination.currentPage);
            params.append('filters', JSON.stringify(filterData));
            params.append('orderBy', this.sortBy);
            params.append('sortedBy', this.sortDesc ? 'desc': 'asc');/* ctx ? ctx.sortDesc ? 'desc' : 'asc' : 'asc' */

            let promise = axios.post(r('dsp.getDataByCompany'), params);
            return promise.then(response => {
                this.userRole = response.data.userRole;
                if (['dev','owner','admin','finance','smanager','manager'].includes(this.userRole)) {
                    this.isManager = true;
                }
                if (['dev','owner','admin','finance'].includes(this.userRole)) {
                    this.isFinance = true;
                }

                const items = this.items.concat(response.data.listDsp.data);

                if (response.data.statusTxt && response.data.statusTxt !== 'All') {
                    this.statusTxt = response.data.statusTxt;
                }
                if (response.data.statusIntegration && response.data.statusIntegration !== 'Integration') {
                    this.statusIntegration = response.data.statusIntegration;
                }
                this.listManagers = response.data.listManagers ? this.prepareListOptions(response.data.listManagers, 'id', 'name') : [];
                this.selectedManager = response.data.activeManager;
                this.regions = response.data.listRegions;
                this.selectedRegion = response.data.selectedRegion;
                this.winRate = response.data.winRate;
                this.dateFrom = response.data.dateFrom;
                this.dateTo = response.data.dateTo;

                this.pagination.endResult = !response.data.listDsp.data.length;

                this.pagination.currentPage = response.data.listDsp.current_page;
                this.pagination.totalPages = response.data.listDsp.last_page;

                this.items = items;

                items.map((row) => {
                    return row;
                });

                return items || [];
            }).catch(() => {
                this.pagination.endResult = true;
                this.pagination.currentPage = 0;
                return [];
            }).finally(() => {
                this.isBusy = false;
            });
        },
        linkGen(pageNum) {
            return pageNum === 1 ? '?' : `?page=${pageNum}`
        },
        changePage(pageNo) {
            this.pagination.currentPage = pageNo;
            this.$refs.dspIndexTable.refresh();
        },
        setStickyHeaderMaxHeight() {
            this.stickyHeaderHeight = window.innerHeight;
        },
        changeEndpoint(item) {
            this.isBusy = true;
            axios.post('save-endpoint-vue', {
                id: item.id,
                endpoint: this.inputSetEndpoint,
            }).then(response => {
                if (response.status === 200) {
                    item.endpoint = this.inputSetEndpoint;
                }
            }).catch((err) => {
                alert(err)
            }).finally(() => {
                document.getElementById('popoverSetEndpointShow_' + item.id).style.display = 'none';
                this.isBusy = false;
            });
        },
        changeLimitQps(item) {
            if (isNaN(this.inputSetLimitQps)) {
                alert('Not a Number!');
                return false;
            }
            if (this.inputSetLimitQps > 0 && this.inputSetLimitQps < 40) {
                alert('QPS value must not be less than 40');
                return;
            }
            this.isBusy = true;
            axios.post('save-qps-vue', {
                id: item.id,
                qps: this.inputSetLimitQps,
            }).then(response => {
                if (response.status === 200) {
                    item.qps = this.inputSetLimitQps;
                }
            }).catch((err) => {
                alert(err)
            }).finally(() => {
                document.getElementById('popoverSetLimitQpsShow_' + item.id).style.display = 'none';
                this.isBusy = false;
            });
        },
        changeSpendLimit(item) {
            if (isNaN(this.inputSetSpendLimit)) {
                alert('Not a Number!');
                return false;
            }
            this.isBusy = true;
            axios.post('vue/changeSpendLimitVue', {
                id: item.id,
                spendlimit: Number(this.inputSetSpendLimit),
            }).then(response => {
                item.spendlimit = Number(this.inputSetSpendLimit);
            }).catch((err) => {
                this.$notify.error(err);
            }).finally(() => {
                document.getElementById('popoverSetSpendLimitShow_' + item.id).style.display = 'none';
                this.isBusy = false;
            });
        },
        changeComment(item) {
            this.isBusy = true;
            axios.post('vue/changeCommentVue', {
                id: item.id,
                comments: this.inputSetComment,
            }).then(response => {
                item.comments = this.inputSetComment;
            }).catch((err) => {
                this.$notify.error(err);
            }).finally(() => {
                document.getElementById('popoverSetCommentShow_' + item.id).style.display = 'none';
                this.isBusy = false;
            });
        },
        prepareApiUrl(item) {
            let link = window.location.origin;
            if (this.project === 'SmartyAds') {
                link += '/api/v1/dsp-report';
            } else if (this.project === 'GothamAds') {
                link += '/api/v1/stats/dsp';
            } else if (this.project === 'BizzClick') {
                link += '/api/v1/daily-stats/dsp';
            } else {
                link += '/api/v1/report/dsp';
            }

            link += '?endpoint=' + encodeURI(item.keyname);
            if (['SmartyAds','GothamAds','BizzClick'].includes(this.project)) {
                link +=
                    '&token=' + item.apikey +
                    '&from=' + this.dateFrom +
                    '&to=' + this.dateTo;
            } else {
                link +=
                    '&apikey=' + item.apikey +
                    '&start=' + this.dateFrom +
                    '&end=' + this.dateTo;
            }

            return link;
        },
        approveEndpoint(item) {
            let conf = confirm("Are you sure want to approve endpoint ID# " + item.id + " ?");
            if (!conf) return;
            this.isBusy = true;
            axios.post('vue/approveEndpointVue/' + item.id).then(response => {
                if (response.data === 'success') {
                    item.active = !item.active;
                    this.isBusy = false;
                }
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        removeEpFromItems(item) {
            const curCompanyIndex = this.items.findIndex((elem)=>elem.company_name === item.company_name);
            this.items[curCompanyIndex].endpoints = this.items[curCompanyIndex].endpoints.filter((elem) => elem.id !== item.id);
        },
        archiveEndpoint(item) {
            let conf = confirm("Are you sure want to archive endpoint ID# " + item.id + " ?");
            if (!conf) return;
            this.isBusy = true;
            axios.post('vue/archiveEndpointVue/' + item.id).then(response => {
                if (response.data === 'success') {
                    this.isBusy = false;
                    this.removeEpFromItems(item);
                    const table = this.$refs.dspEpTable;
                    Vue.nextTick(function () {
                        table.refresh();
                    });
                }
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        deleteEndpoint(item) {
            let conf = confirm("Are you sure want to delete endpoint ID# " + item.id + " ?");
            if (!conf) return;
            this.isBusy = true;
            axios.delete('/dsp/vue/deleteEndpointVue/' + item.id).then(response => {
                if (response.data === 'success') {
                    this.isBusy = false;
                    this.removeEpFromItems(item);
                    const table = this.$refs.dspEpTable;
                    Vue.nextTick(function () {
                        table.refresh();
                    });
                }
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        restoreEndpoint(item) {
            let conf = confirm("Are you sure want to restore endpoint ID# " + item.id + " ?");
            if (!conf) return;
            this.isBusy = true;
            axios.post('vue/restoreEndpointVue/' + item.id).then(response => {
                this.isBusy = false;
                this.removeEpFromItems(item);
                const table = this.$refs.dspEpTable;
                Vue.nextTick(function () {
                    table.refresh();
                });
            }).catch((err) => {
                this.$notify.error(err);
            });
        },
        rowClass(item, type) {
            if (!item || type !== 'row') {
                return '';
            }

            if (item.active === 0) {
                return 'tr-cursor table-tr-not-approved';
            }
            if (item.yesterdayspend && item.spendlimit && (item.yesterdayspend > item.spendlimit)) {
                return 'tr-cursor table-tr-spend-reached';
            }
            if (item.real_qps && !item.bid_qps) {
                return 'tr-cursor table-tr-no-bid';
            }
            return 'tr-cursor';
        },
        parentRowClass(item, type) {
            if (!item || type !== 'row') {
                return '';
            }

            if (item.active === 0) {
                return 'tr-cursor table-tr-not-approved';
            }
            if (item.endpoints.length) {
                const spendReached = item.endpoints.filter(
                    (elem) => elem.yesterdayspend &&
                        elem.spendlimit &&
                        Number(elem.spendlimit) !== 0 &&
                        (Number(elem.yesterdayspend) > Number(elem.spendlimit)));
                if(spendReached.length) {
                    return 'tr-cursor table-tr-spend-reached';
                }
            }
            return 'tr-cursor';
        },
        exportToFile(ext, ctx) {
            this.isBusy = true;

            let filterData = this.setFilterData();

            const params = new URLSearchParams();
            params.append('page', 1);
            params.append('filters', JSON.stringify(filterData));
            params.append('orderBy', this.sortBy);
            params.append('sortedBy', this.sortDesc ? 'desc': 'asc');// ctx ? ctx.sortDesc ? 'desc' : 'asc' : 'asc'
            params.append('export', 1);
            params.append('ext', ext);

            axios({
                url: r('dsp.getDataByCompany'),
                method: 'POST',
                params: params,
                responseType: 'blob',
            }).then(response => {
                this.isBusy = false;
                const url = window.URL.createObjectURL(new Blob([response.data], {type:'text/csv;charset=utf-8;'}));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'list-dsp.' + ext);
                document.body.appendChild(link);
                link.click();
            });
        },
    }
}
</script>

<style lang="scss">
.table {
    border-collapse: separate
}
.table > thead {
    position: relative;
}
.table > thead > tr > th {
    white-space: nowrap;
    vertical-align: middle;
    border:none !important;
    &.b-table-sticky-column {
        z-index: 5 !important;
    }
}

.table.b-table > thead > tr > .table-b-table-default {
    background-color: #3598dc;
    color: #fff;
    text-align: center;
}

.table > tbody > tr > td {
    white-space: nowrap;
    vertical-align: middle;
    text-align: center;
}

tbody th.td-id {
    left: 0;
    z-index: 3 !important;
    top: 56px;
}
thead th.td-id {
    z-index: 3 !important;
    left: 0;
}
tbody td.td-keyname {
    left: 107px !important;
    z-index: 2 !important;

}
thead th.td-keyname {
    z-index: 3 !important;
    left: 107px !important;
}
tbody th.td-keyname {
    z-index: 3 !important;
    left: 107px !important;
    top: 56px;
}
tbody th.td-comp-name {
    z-index: 3 !important;
    top: 56px;
}
.table.b-table,
.table.b-table > tbody > tr > td,
.table.b-table > tbody > tr > th,
.table.b-table > thead > tr > td,
.table.b-table > thead > tr > th {
    border: solid #ddd;
    border-width: 0 1px 1px 0;
}

.table-info, .table-info > td, .table-info > th {
    background-color: #dbf7f9;
}

.tr-cursor {
    cursor: pointer;
}

.table-tr-no-bid > td, .table.b-table .table-tr-no-bid > td.table-b-table-default.b-table-sticky-column {
    background: #dbf7f9;
    border: solid #ddd;
    border-width: 0 1px 1px 0;
    background-image: none !important;
}
.table-tr-spend-reached > td, .table.b-table .table-tr-spend-reached > td.table-b-table-default.b-table-sticky-column {
    background: #fffae9;
    border: solid #ddd;
    border-width: 0 1px 1px 0;
    background-image: none !important;
}
.table-tr-not-approved > td, .table.b-table .table-tr-not-approved > td.table-b-table-default.b-table-sticky-column {
    background: #fbe1e3 !important;
    border: solid #ddd;
    border-width: 0 1px 1px 0;
    background-image: none !important;
}

.table.b-table.table-striped > tbody > tr:nth-of-type(2n+1) > td {
    background-image: linear-gradient(rgba(0, 0, 0, 0.07), rgba(0, 0, 0, 0.07));
}
.vs__dropdown-toggle {
    padding: 2px 0 5px;
}

.v-select {
    .vs__selected-options {
        word-break: break-all;
    }
}

.b-popover {
    padding: 10px;
}

.b-table-sticky-header {
    overflow-y: unset;
    min-height: 380px;
    max-height: none!important;
}

#modalExamples .modal-dialog {
    width: 80%;
}

/*.page-header {
    height: 51px;
}*/
.empty-comment {
    color: #808080;
    &:hover {
        color: #808080;
        text-decoration: underline;
    }
}

.tooltip-inner {
    word-break: break-all;
}
.hidden_header {
    display: none;
}
.filterInput {
    border: 2px solid rgba(60,60,60,.26);
    height: 35px;
    max-width: 200px;
}
table.b-table {
    table-layout: fixed;
    .td-id {
        width: 50px;
        position: sticky;
        left: 0px;
        z-index: 2;
        background: #fff;
        i {
            transition: all 400ms;
            transform: rotate(0);
        }
    }
    .td-companyname {
        width: 200px;
        position: sticky;
        left: 50px;
        z-index: 2;
        background: #fff;
        &.b-table-sticky-column {
            left: 50px !important;
        }
    }
    .td-short {
        width: 35px;
    }
    .td-endpoint {
        width: 200px;
    }
    .td-medium {
        width: 60px;
    }
    .td-region {
        width: 140px;
    }
    .td-spend {
        width: 100px;
    }
    .td-bid {
        width: 100px;
    }
    tr {
        td {
            width: 80px;
        }
    }
    tr {
        th {
            width: 80px;
            white-space: normal;
            font-size: 13px;
        }
    }
    & > tbody {
        tr {
            &.b-table-details {
                .table {
                    margin-bottom: 0;
                }
                & > td {
                    padding: 0;
                }
            }
            &.b-table-has-details {
                & > .td-id {
                    i {
                        transform: rotate(180deg);
                    }
                }
            }
            &.table-tr-no-bid {
                td {
                    background: #dbf7f9;
                }
            }
            &.table-tr-spend-reached {
                td {
                    background: #fffae9;
                }
            }
            & > td {
                white-space: normal;
                word-break: break-all;
                background: #fff;
            }
        }
    }
}
@media screen and (max-width: 1740px) {
.dsp-list-form {
    & > input.form-control {
        margin-bottom: 10px !important;
    }
    & > * {
        margin-bottom: 10px !important;
    }
}
}
</style>
