<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Classes\Settings\CommunicationSettings;
use App\Models\Integrations\Connections\SmsConnection;
use App\Models\People\Person;
use App\Models\Utilities\SchoolMessage;
use Illuminate\Support\Collection;

class LocalSmsConnection extends SmsConnection
{

    /**
     * @inheritDoc
     */
    public static function getSystemInstanceDefault(): array
    {
        /**
         * This code was taken from https://github.com/typpo/textbelt
         */
        return
        [
            'us' =>
            [
                '@email.uscc.net',
                '@message.alltel.com',
                '@messaging.sprintpcs.com',
                '@mobile.celloneusa.com',
                '@msg.telus.com',
                '@paging.acswireless.com',
                '@pcs.rogers.com',
                '@qwestmp.com',
                '@sms.ntwls.net',
                '@tmomail.net',
                '@txt.att.net',
                '@txt.windmobile.ca',
                '@vtext.com',
                '@text.republicwireless.com',
                '@msg.fi.google.com',
            ],
            'canada' =>
            [
                '@blueskyfrog.com',
                '@bplmobile.com',
                '@cellularonewest.com',
                '@clearlydigital.com',
                '@comcastpcs.textmsg.com',
                '@corrwireless.net',
                '@csouth1.com',
                '@cwemail.com',
                '@cwwsms.com',
                '@email.swbw.com',
                '@email.uscc.net',
                '@fido.ca',
                '@ideacellular.net',
                '@inlandlink.com',
                '@ivctext.com',
                '@message.alltel.com',
                '@messaging.centurytel.net',
                '@messaging.sprintpcs.com',
                '@mobile.celloneusa.com',
                '@mobile.dobson.net',
                '@mobile.surewest.com',
                '@mobilecomm.net',
                '@msg.clearnet.com',
                '@msg.koodomobile.com',
                '@msg.telus.com',
                '@my2way.com',
                '@myboostmobile.com',
                '@onlinebeep.net',
                '@page.metrocall.com',
                '@pagemci.com',
                '@paging.acswireless.com',
                '@pcs.rogers.com',
                '@pcsone.net',
                '@qwestmp.com',
                '@satellink.net',
                '@sms.3rivers.net',
                '@sms.bluecell.com',
                '@sms.edgewireless.com',
                '@sms.goldentele.com',
                '@sms.pscel.com',
                '@sms.wcc.net',
                '@text.houstoncellular.net',
                '@text.mtsmobility.com',
                '@tmomail.net',
                '@tms.suncom.com',
                '@txt.att.net',
                '@txt.bell.ca',
                '@txt.northerntelmobility.com',
                '@txt.windmobile.ca',
                '@uswestdatamail.com',
                '@utext.com',
                '@vmobile.ca',
                '@vmobl.com',
                '@vtext.com',
            ],
            'intl' =>
            [
                '@airtelchennai.com',
                '@airtelkol.com',
                '@airtelmail.com',
                '@alphame.com',
                '@bluewin.ch',
                '@bplmobile.com',
                '@c.vodafone.ne.jp',
                '@celforce.com',
                '@correo.movistar.net',
                '@delhi.hutch.co.in',
                '@digitextjm.com',
                '@e-page.net',
                '@escotelmobile.com',
                '@freesurf.ch',
                '@gsm1800.telia.dk',
                '@h.vodafone.ne.jp',
                '@ideacellular.net',
                '@itelcel.com',
                '@m1.com.sg',
                '@ml.bm',
                '@mmail.co.uk',
                '@mobilpost.no',
                '@mobistar.be',
                '@mobtel.co.yu',
                '@movistar.net',
                '@msgnextel.com.mx',
                '@msg.globalstarusa.com',
                '@msg.iridium.com',
                '@mujoskar.cz',
                '@mymeteor.ie',
                '@mysmart.mymobile.ph',
                '@mysunrise.ch',
                '@o2.co.uk',
                '@o2imail.co.uk',
                '@onemail.at',
                '@onlinebeep.net',
                '@optusmobile.com.au',
                '@page.mobilfone.com',
                '@page.southernlinc.com',
                '@pageme.teletouch.com',
                '@pager.irkutsk.ru',
                '@pcs.ntelos.com',
                '@rek2.com.mx',
                '@rpgmail.net',
                '@safaricomsms.com',
                '@satelindogsm.com',
                '@scs-900.ru',
                '@sfr.fr',
                '@sms.co.tz',
                '@sms.comviq.se',
                '@sms.emt.ee',
                '@sms.goldentele.com',
                '@sms.luxgsm.lu',
                '@sms.netcom.no',
                '@sms.primtel.ru',
                '@sms.t-mobile.at',
                '@sms.tele2.lv',
                '@sms.umc.com.ua',
                '@sms.uraltel.ru',
                '@sms.vodafone.it',
                '@smsmail.lmt.lv',
                '@swmsg.com',
                '@t-d1-sms.de',
                '@t-mobile-sms.de',
                '@t-mobile.uk.net',
                '@t.vodafone.ne.jp',
                '@text.mtsmobility.com',
                '@text.simplefreedom.net',
                '@timnet.com',
                '@vodafone.net',
                '@wyndtell.com',
            ],
        ];
    }

    public function sendToNumber(string $number, string $message): void
    {
        $commSettings = app(CommunicationSettings::class);
        $emailConnection = $commSettings->email_connection;
        foreach($this->data->us as $domain)
            $emailConnection->sendToPersonSimple($number.$domain, '', $message);
    }
}