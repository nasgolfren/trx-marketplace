<script setup>
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { reactive, ref, computed, onMounted, onBeforeMount } from 'vue';

import Menubar from 'primevue/menubar';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import SplitButton from 'primevue/splitbutton';
import Fieldset from 'primevue/fieldset';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Image from 'primevue/image';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import Checkbox from 'primevue/checkbox';
import Card from 'primevue/card';
import NProgress from 'nprogress';

import moment from 'moment';
import numeral from 'numeral';
import { convertJsonToReadableText } from '@/functions';


/**
 * App - setup
 */
const confirm = useConfirm();
const toast = useToast();

/**
 * App - controlls
 */
const orderDetails = ref(null);
const showOrderDetailsModal = ref(false);
const walletLoading = ref(false);
const tronWallet = ref();
let refreshWalletInterval = null;

const formOrder = useForm({
    amount: 0,
    price: 0,
    source_address: '',
    selectedResource: {},
    selectedDurationSource: {},
    partial_fill: true,
    multisignature: true,
});

const props = defineProps({
    orders: Array,
    durations: Array,
    resources: Array,
    formConfig: Object,
    connectedWallet: Object,
    targetAddress: String,
    tronscanTransaction: String,
    tronscanAdress: String,
});

const buttonitems = [
    {
        label: 'Disconnect Wallet',
        icon: 'pi pi-power-off',
        command: () => {
            tronWallet.value = null;
            delete formOrder.errors.source_address;

            clearInterval(refreshWalletInterval);
            refreshWalletInterval = null;

            NProgress.start();

            router.delete('/wallet-info', {
                preserveState: true,
                onSuccess: page => {
                    toast.add({ severity: 'info', summary: 'Info', detail: 'Wallet was disconnected', life: 3000 });
                },
                onError: errors => {
                    toast.add({ severity: 'error', summary: 'Error', detail: error, life: 5000 });
                },
                onFinish: visit => {
                    NProgress.done();
                },
            });
        }
    },
];

/**
 *
 * App init
 *
 */
onBeforeMount(() => {
    formOrder.amount = props.formConfig.energy.minAmountValue;
    formOrder.selectedResource = props.resources.find((resource) => resource.code == 'energy');
    formOrder.selectedDurationSource = props.formConfig.energy.durations.find((duration) => duration.code == 72);
    formOrder.price = formOrder.selectedDurationSource.price;

    clearInterval(refreshWalletInterval);
    refreshWalletInterval = null;
})

onMounted(async () => {
    if (props.connectedWallet) {
        await initTrxWallet();

        formOrder.source_address = props.connectedWallet.address;
    }

    // Tronlink events
    window.addEventListener('message', async function (e) {
        if (e.data.message && e.data.message.action == "tabReply") {
            console.log("tabReply event", e.data.message)

            if (e.data.message.data.data.node && e.data.message.data.data.node.chain == '_'){
                console.log("tronLink currently selects the main chain 1")
            }else{
                console.log("tronLink currently selects the side chain 1 - else")
            }
        }

        if (e.data.message && e.data.message.action == "setAccount") {
            console.log("setAccount event", e.data.message)
            console.log("current address:", e.data.message.data.address)

            if (props.connectedWallet) {
                await __showTronInfo(true, e.data.message.data.address);
            }
        }

        if (e.data.message && e.data.message.action == "setNode") {
            console.log("setNode event", e.data.message)
            if (e.data.message.data.node.chain == '_'){
                console.log("tronLink currently selects the main chain 2")
            }else{
                console.log("tronLink currently selects the side chain 2")
            }

            // Tronlink chrome v3.22.1 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "connect") {
                console.log("connect event", e.data.message.isTronLink)
            }

            // Tronlink chrome v3.22.1 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "disconnect") {
                console.log("disconnect event", e.data.message.isTronLink)
            }

            // Tronlink chrome v3.22.0 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "accountsChanged") {
                console.log("accountsChanged event", e.data.message)
                console.log("current address:", e.data.message.data.address)
            }

            // Tronlink chrome v3.22.0 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "connectWeb") {
                console.log("connectWeb event", e.data.message)
                console.log("current address:", e.data.message.data.address)
            }

            // Tronlink chrome v3.22.0 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "accountsChanged") {
                console.log("accountsChanged event", e.data.message)
            }

            // Tronlink chrome v3.22.0 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "acceptWeb") {
                console.log("acceptWeb event", e.data.message)
            }
            // Tronlink chrome v3.22.0 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "disconnectWeb") {
                console.log("disconnectWeb event", e.data.message)
            }

            // Tronlink chrome v3.22.0 & Tronlink APP v4.3.4 started to support
            if (e.data.message && e.data.message.action == "rejectWeb") {
                console.log("rejectWeb event", e.data.message)
            }
        }
    });
})


const initTrxWallet = async (initProcess = true) => {
    try {
        walletLoading.value = true;

        let obj = setTimeout(async function () {
            if (window.tronLink.ready) {
                tronWeb = tronLink.tronWeb;
            } else {
                const res = await tronLink.request({ method: 'tron_requestAccounts' });

                if (res.code === 200) {
                    tronWeb = tronLink.tronWeb;
                }
            }

            tronWallet.value = tronWeb;

            await __showTronInfo(initProcess);

            clearInterval(obj);
        }, 50);
    } catch (error) {
        walletLoading.value = false;
        console.log('initTrxWallet - try: ', error);
    }
}

async function __showTronInfo(initProcess = true, changedWallet = null) {
    try {
        let localWalletObj = tronWallet.value;

        if (localWalletObj == undefined) {
            initTrxWallet(false);
            return;
        }

        let walletAddress = changedWallet != null ? changedWallet : localWalletObj.defaultAddress.base58;

        if (!walletAddress) {
            throw "Wallet is not connected";
        }

        delete formOrder.errors.source_address;

        if (initProcess) {
            NProgress.start();
        }

        router.post('/wallet-info', {
            'wallet_address': walletAddress,
        },{
            preserveState: true,
            preserveScroll: true,
            onSuccess: page => {
                if (initProcess) {
                    formOrder.source_address = props.connectedWallet.address;
                    toast.add({ severity: 'info', summary: 'Info', detail: 'Wallet successfully connected', life: 3000 });
                }

                refreshWallet();
            },
            onError: errors => {
                console.log('Error saving data to session:', errors);

                if (initProcess) {
                    if (typeof errors == 'object') {
                        toast.add({ severity: 'error', summary: 'Error', detail: errors[0], life: 5000 });
                    } else {
                        toast.add({ severity: 'error', summary: 'Error', detail: 'Wallet was not connected!', life: 5000 });
                    }
                }
            },
            onFinish: visit => {
                walletLoading.value = false;

                if (initProcess) {
                    NProgress.done();
                }
            },
        });

    } catch (error) {
        walletLoading.value = false;
        console.log('__showTronInfo error: ', error);
        toast.add({ severity: 'error', summary: 'Error', detail: error + ', Try to connect to your TronLink first', life: 5000 });
    }
}

const refreshWallet = async () => {
    if (!refreshWalletInterval) {
        refreshWalletInterval = setInterval(async function () {
            console.log('interval!');
            console.log('props.connectedWallet', props.connectedWallet);
            console.log('refreshWalletInterval', refreshWalletInterval);

            if (props.connectedWallet) {
                await __showTronInfo(false);
            }
        }, 15000);
    }
}


/**
 *
 * Form - setup
 *
 */
const formConfigDurations = computed(() => {
    return props.formConfig[formOrder.selectedResource.code].durations;
});

const minAmountValue = computed(() => {
    return props.formConfig[formOrder.selectedResource.code].minAmountValue;
});

const minPriceValue = computed(() => {
    return props.formConfig[formOrder.selectedResource.code].durations.find((duration) => duration.code == formOrder.selectedDurationSource.code).price;
});

/**
 *
 * Form - live data handeling
 *
 */
const updateAmount = (event) => {
    formOrder.amount = event.value;
};
const updatePrice = (event) => {
    formOrder.price = event.value;
};

const changeResource = (event) => {
    formOrder.amount = minAmountValue.value;
    formOrder.price = props.formConfig[formOrder.selectedResource.code].durations.find((duration) => duration.code == formOrder.selectedDurationSource.code).price;

    formOrder.selectedDurationSource = props.formConfig[formOrder.selectedResource.code].durations.find((duration) => duration.code == formOrder.selectedDurationSource.code);
};
const changeDuration = (event) => {
    formOrder.price = props.formConfig[formOrder.selectedResource.code].durations.find((duration) => duration.code == formOrder.selectedDurationSource.code).price;
};

const tronAddressValidity = async () => {
    if (formOrder.source_address == '' || formOrder.source_address.length < 34) {
        return;
    }
    delete formOrder.errors.source_address;

    const options = {
        method: 'POST',
        headers: { accept: 'application/json', 'content-type': 'application/json' },
        body: JSON.stringify({ address: formOrder.source_address, visible: true })
    };

    await fetch('https://api.shasta.trongrid.io/wallet/validateaddress', options)
        .then(response => response.json())
        .then(function (response) {
            if (response.result == false) {
                formOrder.errors.source_address = 'Tron address is not valid';
            }
        })
        .catch(function (error) {

            formOrder.errors.source_address = 'Something goes wrong, try it again or contact admin!';
        });
};

const balanceTrx = computed(() => {
    return !props.connectedWallet ? 0 : numeral(props.connectedWallet.wallet_balance).format('0,00.00');
});
const stakedTrx = computed(() => {
    return !props.connectedWallet ? 0 : numeral(props.connectedWallet.wallet_staked_trx).format('0,0');
});
const allTrx = computed(() => {
    let balance = !props.connectedWallet ? 0 : props.connectedWallet.wallet_balance;
    let freeze = !props.connectedWallet ? 0 : props.connectedWallet.wallet_staked_trx;

    return numeral(balance + freeze).format('0,0.00');
});

const energyRemaining = computed(() => {
    return !props.connectedWallet ? 0 : numeral(props.connectedWallet.bandwidth.energyRemaining).format('0,0');
});
const energyLimit = computed(() => {
    return !props.connectedWallet ? 0 : numeral(props.connectedWallet.bandwidth.energyLimit).format('0,0');
});

const netRemaining = computed(() => {
    return !props.connectedWallet ? 0 : numeral((props.connectedWallet.bandwidth.freeNetRemaining + props.connectedWallet.bandwidth.netRemaining)).format('0,0');
});
const netLimit = computed(() => {
    return !props.connectedWallet ? 0 : numeral((props.connectedWallet.bandwidth.freeNetLimit + props.connectedWallet.bandwidth.netLimit)).format('0,0')
});

const delegatedEnergy = computed(() => {
    let trx = !props.connectedWallet ? 0 : props.connectedWallet.delegatedFrozenV2BalanceForEnergy;
    let cost = !props.connectedWallet ? 0 : props.connectedWallet.energyCost;

    return !props.connectedWallet ? 0 : numeral((trx * cost) / 1000000).format('0,0');
});
const delegatedBandwith = computed(() => {
    let bandwith = !props.connectedWallet ? 0 : props.connectedWallet.delegatedFrozenV2BalanceForBandwidth;
    let netCost = !props.connectedWallet ? 0 : props.connectedWallet.netCost;

    return !props.connectedWallet ? 0 : numeral(bandwith * netCost).format('0,0');
});

/**
 *
 * Form - order data
 *
 */
const finalPrice = computed(() => {
    let result = formOrder.amount * formOrder.price;
    let duration = isValueHours(formOrder.selectedDurationSource.code) ? formOrder.selectedDurationSource.code : (formOrder.selectedDurationSource.code / 24);

    return result ? numeral(((result / 1000000) * duration)).format('0,00.00') : 0;
});

const isSubmitButtonDisabled = computed(() => {
    return Object.keys(formOrder.errors).length >= 1
        || !formOrder.source_address || formOrder.source_address.length < 34;
});

const formOrderSubmit = () => {
    confirm.require({
        group: 'headless',
        header: 'Create the order',
        message: 'Please confirm to proceed the order',
        accept: async () => {
            try {
                if (finalPrice.value > props.connectedWallet.wallet_balance) {
                    throw "Your TRX balance is not enought!";
                }

                let tronWeb = tronWallet.value;

                var tx = await tronWeb.transactionBuilder.sendTrx(props.targetAddress, finalPrice.value, formOrder.source_address);
                var signedTx = await tronWeb.trx.sign(tx);
                var broastTx = await tronWeb.trx.sendRawTransaction(signedTx);

                if (!broastTx.result) {
                    throw "Something wrong happend on blockchain!";
                }

                formOrder.transform((data) => ({
                    ...data,
                    'resource': data.selectedResource.code,
                    'hours': data.selectedDurationSource.code,
                    'total': finalPrice.value,
                    'txid': broastTx.txid,
                }))
                .post('/orders', {
                    errorBag: 'submitOrder',
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        toast.add({ severity: 'success', summary: 'Success', detail: 'The order has been successfully created!', life: 3000 });
                    },
                    onError: (errors) => {
                        toast.add({ severity: 'error', summary: 'Error', detail: convertJsonToReadableText(errors), life: 5000 });
                        console.log("error /orders - ", convertJsonToReadableText(errors));
                    }
                });
            } catch (error) {
                console.log('formOrderSubmit() - error: ', error);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Order process interupted - ' + error, life: 5000 });
            }
        },
        reject: () => {
            toast.add({ severity: 'info', summary: 'Cancelled', detail: 'You cancelled the order', life: 3000 });
        },
        onHide: () => {
            toast.add({ severity: 'info', summary: 'Cancelled', detail: 'You cancelled the order', life: 3000 });
        },
    });
};


/**
 *
 * Utility functions
 *
 */
 async function copyTrxWallet() {
    try {
        await navigator.clipboard.writeText(props.connectedWallet.address);
        toast.add({ severity: 'info', summary: 'Info', detail: 'Address was copied to clipboard', life: 3000 });
    } catch (error) {
        console.log('error: ', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Copy process interupted', life: 5000 });
    }
}

const isValueHours = (value) => {
    return (value % 24) > 0;
};

const showOrderDetails = (order) => {
    orderDetails.value = order;
    showOrderDetailsModal.value = true;
};

const closeOrderDetailsModal = () => {
    orderDetails.value = null;
    showOrderDetailsModal.value = false;
};
</script>

<template>
    <Head title="Adainvest" />

    <div class="container mx-auto p-2 sm:p-0 mb-5">
        <Menubar class="my-8 border-none bg-white">
            <template #start>
                <svg width="35" height="40" viewBox="0 0 35 40" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="h-8">
                    <path
                        d="M25.87 18.05L23.16 17.45L25.27 20.46V29.78L32.49 23.76V13.53L29.18 14.73L25.87 18.04V18.05ZM25.27 35.49L29.18 31.58V27.67L25.27 30.98V35.49ZM20.16 17.14H20.03H20.17H20.16ZM30.1 5.19L34.89 4.81L33.08 12.33L24.1 15.67L30.08 5.2L30.1 5.19ZM5.72 14.74L2.41 13.54V23.77L9.63 29.79V20.47L11.74 17.46L9.03 18.06L5.72 14.75V14.74ZM9.63 30.98L5.72 27.67V31.58L9.63 35.49V30.98ZM4.8 5.2L10.78 15.67L1.81 12.33L0 4.81L4.79 5.19L4.8 5.2ZM24.37 21.05V34.59L22.56 37.29L20.46 39.4H14.44L12.34 37.29L10.53 34.59V21.05L12.42 18.23L17.45 26.8L22.48 18.23L24.37 21.05ZM22.85 0L22.57 0.69L17.45 13.08L12.33 0.69L12.05 0H22.85Z"
                        class="fill-primary-500 dark:fill-primary-400" />
                    <path
                        d="M30.69 4.21L24.37 4.81L22.57 0.69L22.86 0H26.48L30.69 4.21ZM23.75 5.67L22.66 3.08L18.05 14.24V17.14H19.7H20.03H20.16H20.2L24.1 15.7L30.11 5.19L23.75 5.67ZM4.21002 4.21L10.53 4.81L12.33 0.69L12.05 0H8.43002L4.22002 4.21H4.21002ZM21.9 17.4L20.6 18.2H14.3L13 17.4L12.4 18.2L12.42 18.23L17.45 26.8L22.48 18.23L22.5 18.2L21.9 17.4ZM4.79002 5.19L10.8 15.7L14.7 17.14H14.74H15.2H16.85V14.24L12.24 3.09L11.15 5.68L4.79002 5.2V5.19Z"
                        class="fill-surface-700 dark:fill-surface-0/80" />
                </svg>
            </template>
            <template #end>
                <div class="flex items-center gap-2">
                    <Button v-if="!connectedWallet" @click="initTrxWallet()" size="small" outlined :loading="walletLoading" :disabled="walletLoading">
                        <Image v-if="!walletLoading" src="/images/tronlink_icon.png" alt="Image" width="20px" />
                        <span v-if="!walletLoading" class="px-3 font-semibold">Connect wallet</span>
                        <span v-else class="pi pi-spin pi-cog" style="font-size: 1.5rem"></span>
                    </Button>
                    <SplitButton v-else :model="buttonitems" @click="copyTrxWallet()" size="small">
                        <span class="pi pi-copy"></span>
                        <span class="ml-2 flex items-center font-bold break-all">{{ connectedWallet.address }}</span>
                    </SplitButton>
                </div>
            </template>
        </Menubar>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8 mb-5">
            <div class="xl:col-end-3">
                <Card class="overview-card">
                    <template #title>
                        <p>
                            <span class="pi pi-user mr-3"></span>Account
                        </p>
                        <span class="text-base font-thin text-gray-600 icon-container break-all">
                            {{ !connectedWallet ? '-' : connectedWallet.address }}
                            <Button v-if="connectedWallet" @click="copyTrxWallet()"
                                class="py-[2px] hidden sm:inline-flex" icon="pi pi-copy" text
                                aria-label="copy wallet" />
                        </span>
                    </template>
                    <template #subtitle>
                        <div class="grid gap-4 grid-cols-3">
                            <div>
                                <span class="text-xs text-gray-500">Balance (TRX)</span>
                                <p class="text-xl font-semibold">{{ balanceTrx }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Staked (TRX)</span>
                                <p class="text-xl font-semibold">{{ stakedTrx }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">All (TRX)</span>
                                <p class="text-xl font-semibold">{{ allTrx }}</p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
            <div>
                <Card class="overview-card">
                    <template #title>
                        <div class="grid grid-cols-2">
                            <div>
                                <p>
                                    <span class="pi pi-bolt mr-3 text-blue-600 font-semibold"></span>Energy
                                </p>
                                <span class="text-base font-thin text-gray-600 icon-container">
                                    {{ energyRemaining }}
                                    <span class="text-slate-400">&nbsp;/&nbsp;{{ energyLimit }}</span>
                                </span>
                            </div>
                            <div>
                                <p class="icon-container">
                                    <span class="material-symbols-outlined mr-3 text-emerald-600 font-bold">avg_pace</span>Bandwidth
                                </p>
                                <span class="text-base font-thin text-gray-600 icon-container">
                                    {{ netRemaining }}
                                    <span class="text-slate-400">&nbsp;/&nbsp;{{ netLimit }}</span>
                                </span>
                            </div>
                        </div>
                    </template>
                    <template #subtitle>
                        <div class="grid gap-4 grid-cols-2">
                            <div>
                                <span class="text-xs text-gray-500">Delegated Energy</span>
                                <p class="text-xl font-semibold">{{ delegatedEnergy }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Delegated Bandwidth</span>
                                <p class="text-xl font-semibold">{{ delegatedBandwith }}</p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <div class="grid gap-4 grid-cols-3 grid-rows-1">
            <div class="col-span-full lg:col-span-1" v-focustrap>
                <form @submit.prevent="formOrderSubmit">
                <Fieldset>
                    <template #legend>
                        <div class="flex items-center gap-2 px-2">
                            <span class="pi pi-shopping-cart mr-2 text-primary-500"></span>
                            <span class="font-bold">Order Form</span>
                        </div>
                    </template>
                    <div class="flex flex-col">
                        <div class="w-full">
                            <label class="block text-xs font-medium text-gray-900">
                                Buy amount <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The amount expected in energy or bandwidth'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputNumber v-model="formOrder.amount" @input="updateAmount" class="w-full" inputId="integeronly" showButtons :min="minAmountValue" :max="500000000" :step="1000" :invalid="formOrder.errors.amount" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.amount }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Resource <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The resource type for the order'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <Dropdown v-model="formOrder.selectedResource" :options="resources" @change="changeResource" class="w-full" optionLabel="name" :invalid="formOrder.errors.resource" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.resource }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Duration <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The duration of the rented resource in days'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <Dropdown v-model="formOrder.selectedDurationSource" @change="changeDuration" :options="formConfigDurations" class="w-full" optionLabel="name" :invalid="formOrder.errors.hours" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.hours }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Price <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The price/day in SUN for the expected resource'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputNumber v-model="formOrder.price" @input="updatePrice" class="w-full" inputId="integeronly" showButtons :min="minPriceValue" :step="10" :invalid="formOrder.errors.price" />
                                <div class="text-red-500 text-xs">{{ formOrder.errors.price }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Target <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The target address of the energy / bandwidth obtained. It cannot be a contract address or any other invalid address'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                <InputGroup>
                                    <InputGroupAddon>
                                        <i class="pi pi-wallet"></i>
                                    </InputGroupAddon>
                                    <InputText type="text" v-model="formOrder.source_address" @input="tronAddressValidity" class="w-full" placeholder="TRX address" :invalid="formOrder.errors.source_address" />
                                </InputGroup>
                                <div class="text-red-500 text-xs">{{ formOrder.errors.source_address }}</div>
                            </div>
                        </div>

                        <div class="w-full mt-4">
                            <label class="block text-xs font-medium text-gray-900">
                                Options
                            </label>
                            <div class="mt-1">
                                <div>
                                    <Checkbox v-model="formOrder.partial_fill" inputId="partial_fill" :binary="true" />
                                    <label for="partial_fill" class="ml-2">
                                        Allow Partial Fill
                                        <span class="pi pi-question-circle text-primary-500 text-sm pl-1" v-tooltip.top="'If checked it will allow the order to be filled partially. If unchecked the order won\'t be filled unless there is 1 address which can complete the order in 1 transaction.'" placeholder="Top"></span>
                                    </label>
                                </div>
                                <div class="mt-1">
                                    <Checkbox v-model="formOrder.multisignature" inputId="multisignature" :binary="true" />
                                    <label for="multisignature" class="ml-2">
                                        Multisignature
                                        <span class="pi pi-question-circle text-primary-500 text-sm pl-1" v-tooltip.top="'If checked a multisignature popup appear to create the multisignature transaction. This works for any kind of multisignature address.'" placeholder="Top"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="flex justify-between">
                                <div>{{ formOrder.selectedResource.name }}</div>
                                <div v-if="formOrder.selectedResource.code == 'energy'" class="text-blue-600 font-semibold">
                                    {{ formOrder.amount }}<span class="pi pi-bolt"></span>
                                </div>
                                <div v-else class="text-emerald-600 font-semibold icon-container">
                                    {{ formOrder.amount }}<span class="material-symbols-outlined material-icon-small">avg_pace</span>
                                </div>
                            </div>
                            <div class="flex justify-between mt-2 font-semibold">
                                <div>
                                    Total <span class="pi pi-question-circle text-primary-500 text-sm" v-tooltip.top="'Total = amount * price * duration'" placeholder="Top"></span>
                                </div>
                                <div>{{ finalPrice }} TRX</div>
                            </div>
                        </div>

                        <Button class="mt-4" type="submit" label="BUY" severity="success" :disabled="formOrder.processing || isSubmitButtonDisabled" />
                    </div>
                </Fieldset>
                </form>
            </div>

            <div class="col-span-full lg:col-span-2 order-book">
                <Fieldset>
                    <template #legend>
                        <div class="flex items-center gap-2 px-2">
                            <span class="pi pi-book mr-2 text-primary-500"></span>
                            <span class="font-bold">Orders</span>
                        </div>
                    </template>

                    <DataTable :value="orders" stripedRows paginator :size="'small'" :rows="10" :rowsPerPageOptions="[10, 20, 50]">
                        <Column header="">
                            <template #body="{ data }">
                                <Button icon="pi pi-info-circle" severity="info" text outlined rounded aria-label="User" @click="showOrderDetails(data)" />
                            </template>
                        </Column>
                        <Column field="created_at" header="Date">
                            <template #header>
                                <span class="pi pi-question-circle text-primary-500 order-last ml-2" v-tooltip.top="'The date when the order has been created'" placeholder="Top"></span>
                            </template>
                            <template #body="{ data }">
                                {{ moment(data.created_at).format('h:mm') }}
                                <div class="text-xs">{{ moment(data.created_at).format('MM-DD') }}</div>
                            </template>
                        </Column>
                        <Column field="amount" header="Resource" class="hide-on-mobile">
                            <template #header>
                                <span class="pi pi-question-circle text-primary-500 order-last ml-2" v-tooltip.top="'Type of resource'" placeholder="Top"></span>
                            </template>
                            <template #body="{ data }">
                                <div v-if="data.resource == 'energy'" class="text-blue-600 font-semibold">
                                    {{ data.amount }}<span class="pi pi-bolt"></span>
                                </div>
                                <div v-else class="text-emerald-600 font-semibold icon-container">
                                    {{ data.amount }}<span class="material-symbols-outlined material-icon-small">avg_pace</span>
                                </div>

                                <div class="text-xs">
                                    / {{ data.hours }}
                                    <span v-if="isValueHours(data.hours)">hours</span>
                                    <span v-else>days</span>
                                </div>
                            </template>
                        </Column>
                        <Column header="Price">
                            <template #header>
                                <span class="pi pi-question-circle text-primary-500 order-last ml-2" v-tooltip.top="'The price/day in SUN for resource unit. Note: 1 SUN = 0.000001 TRX'" placeholder="Top"></span>
                            </template>
                            <template #body="{ data }">
                                {{ numeral(data.total).format('0,00.00') }}
                            </template>
                        </Column>
                        <Column header="Payout">
                            <template #header>
                                <span class="pi pi-question-circle text-primary-500 order-last ml-2" v-tooltip.top="'The payout to the seller / the payment of the buyer'" placeholder="Top"></span>
                            </template>
                            <template #body="{ data }">
                                <div class="text-blue-600 font-semibold">
                                    {{ numeral(data.total).format('0,00.00') * 0.7 }} TRX
                                </div>

                                <div class="text-xs">
                                    {{ numeral(data.total).format('0,00.00') }} TRX
                                </div>
                            </template>
                        </Column>
                        <Column header="Fullfilled" class="hide-on-mobile">
                            <template #header>
                                <span class="pi pi-question-circle text-primary-500 order-last ml-2" v-tooltip.top="'A percentage how much the order is completed'" placeholder="Top"></span>
                            </template>
                            <template #body="{ data }">
                                <ProgressBar :value="50"></ProgressBar>
                            </template>
                        </Column>
                    </DataTable>
                </Fieldset>
            </div>
        </div>
    </div>

    <Toast style="top: 75px;" />

    <Dialog v-model:visible="showOrderDetailsModal" modal :breakpoints="{ '1199px': '75vw', '575px': '95vw' }">
        <template #header>
            <div class="inline-flex items-center justify-center gap-2">
                <span class="pi pi-book mr-2 text-primary-500"></span>
                <span class="font-bold whitespace-nowrap font-bold font-mono ">Order details</span>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-3 mt-3 content-center break-all">
            <div class="grid grid-cols-1 md:grid-cols-3">
                <div class="font-bold">Target Address</div>
                <div class="col-span-2">
                    <a :href="props.tronscanAdress + orderDetails.target_address" target="_blank" rel="noopener noreferrer" class="p-button text-primary-500">
                        {{ orderDetails.target_address }}
                        <span class="pi pi-external-link ml-2 text-primary-500"></span>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-3">
                <div class="font-bold">Price</div>
                <div class="col-span-2 text-right md:text-left">{{ numeral(orderDetails.total).format('0,00.00') }}</div>
            </div>
            <div class="grid grid-cols-3">
                <div class="font-bold">Payout</div>
                <div class="col-span-2 text-right md:text-left">
                    <div class="text-blue-600 font-semibold">
                        {{ numeral(orderDetails.total).format('0,00.00') * 0.7 }} TRX
                    </div>

                    <div class="text-xs">
                        {{ numeral(orderDetails.total).format('0,00.00') }} TRX
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3">
                <div class="font-bold">Fullfilled</div>
                <div class="col-span-2 text-right md:text-left">xxx %</div>
            </div>
            <div class="grid grid-cols-3">
                <div class="font-bold">Created at</div>
                <div class="col-span-2 text-right md:text-left">{{ moment(orderDetails.created_at).format('YYYY-MM-DD h:mm') }}</div>
            </div>
            <div class="grid grid-cols-3">
                <div class="font-bold">Transaction</div>
                <div class="col-span-2">
                    <a :href="props.tronscanTransaction + orderDetails.txid" target="_blank" rel="noopener noreferrer" class="p-button text-primary-500">
                        {{ orderDetails.txid }}
                        <span class="pi pi-external-link ml-2 text-primary-500"></span>
                    </a>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Ok" @click="closeOrderDetailsModal()" text />
        </template>
    </Dialog>

    <ConfirmDialog group="headless">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="flex flex-col items-center p-5 bg-surface-0 dark:bg-surface-700 rounded-md">
                <div class="rounded-full bg-primary-500 dark:bg-primary-400 text-surface-0 dark:text-surface-900 inline-flex justify-center items-center h-[6rem] w-[6rem] -mt-[3rem]">
                    <i class="pi pi-question text-5xl"></i>
                </div>
                <span class="font-bold text-2xl block mb-2 mt-4">{{ message.header }}</span>
                <p class="mb-0">{{ message.message }}</p>

                <div class="my-4">

                    <div class="flex flex-col">
                        <div class="w-full">
                            <label class="text-xs font-medium text-gray-900">
                                Amount <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The amount expected in energy or bandwidth'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                {{ formOrder.amount }}
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Resource <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The resource type for the order'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                {{ formOrder.selectedResource.name }}
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Duration <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The duration of the rented resource in days'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                {{ formOrder.selectedDurationSource.name }}
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Price <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The price/day in SUN for the expected resource'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1">
                                {{  formOrder.price  }}
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <label class="block text-xs font-medium text-gray-900">
                                Target TRX Address <span class="pi pi-question-circle text-primary-500" v-tooltip.top="'The target address of the energy / bandwidth obtained. It cannot be a contract address or any other invalid address'" placeholder="Top"></span>
                            </label>
                            <div class="mt-1 break-all">
                                {{ formOrder.source_address }}
                            </div>
                        </div>

                        <div class="w-full mt-4">
                            <label class="block text-xs font-medium text-gray-900">
                                Options
                            </label>
                            <div class="mt-1">
                                <div>
                                    <Checkbox v-model="formOrder.partial_fill" inputId="partial_fill" :binary="true" disabled />
                                    <label for="partial_fill" class="ml-2">
                                        Allow Partial Fill
                                        <span class="pi pi-question-circle text-primary-500 text-sm pl-1" v-tooltip.top="'If checked it will allow the order to be filled partially. If unchecked the order won\'t be filled unless there is 1 address which can complete the order in 1 transaction.'" placeholder="Top"></span>
                                    </label>
                                </div>
                                <div class="mt-1">
                                    <Checkbox v-model="formOrder.multisignature" inputId="multisignature" :binary="true" disabled />
                                    <label for="multisignature" class="ml-2">
                                        Multisignature
                                        <span class="pi pi-question-circle text-primary-500 text-sm pl-1" v-tooltip.top="'If checked a multisignature popup appear to create the multisignature transaction. This works for any kind of multisignature address.'" placeholder="Top"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div>
                            <div class="flex justify-between">
                                <div>{{ formOrder.selectedResource.name }}</div>
                                <div v-if="formOrder.selectedResource.code == 'energy'" class="text-blue-600 font-semibold">
                                    {{ formOrder.amount }}<span class="pi pi-bolt"></span>
                                </div>
                                <div v-else class="text-emerald-600 font-semibold icon-container">
                                    {{ formOrder.amount }}<span class="material-symbols-outlined material-icon-small">avg_pace</span>
                                </div>
                            </div>
                            <div class="flex justify-between mt-2 font-semibold">
                                <div>
                                    Total <span class="pi pi-question-circle text-primary-500 text-sm" v-tooltip.top="'Total = amount * price * duration'" placeholder="Top"></span>
                                </div>
                                <div>{{ finalPrice }} TRX</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!connectedWallet" class="flex items-center mt-4">
                    <Button v-if="!connectedWallet" @click="initTrxWallet()" outlined :loading="walletLoading" :disabled="walletLoading">
                        <Image v-if="!walletLoading" src="/images/tronlink_icon.png" alt="Image" width="20px" />
                        <span v-if="!walletLoading" class="px-3 font-semibold">Connect wallet</span>
                        <span v-else class="pi pi-spin pi-cog" style="font-size: 1.5rem"></span>
                    </Button>
                    <Button label="Cancel" class="ml-4" severity="danger" outlined @click="rejectCallback"></Button>
                </div>
                <div v-else class="flex items-center gap-2 mt-4">
                    <Button label="Confirm Order" @click="acceptCallback"></Button>
                    <Button label="Cancel" severity="danger" outlined @click="rejectCallback"></Button>
                </div>
            </div>
        </template>
    </ConfirmDialog>
</template>

<style>

</style>
