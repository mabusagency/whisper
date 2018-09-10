<?php

namespace App\Helpers;

use App\Field;

class FieldHelper
{

    public function create_field($tag, $campaign) {

        $sh = new StudentHelper();
        $tag = $sh->format_field_for_mailchimp_tag($tag);

        //Make its unique if it already exists
        if (Field::where('institution_id', $campaign->institution_id)->where('tag', $tag)->first()) {
            $tag = substr($tag, 0, 8) . rand(10, 99);
        }

        $field = new Field();
        $field->institution_id = $campaign->institution_id;
        $field->name = $tag;
        $field->tag = $tag;
        $field->save();

        $mh = new MailChimpHelper($campaign);
        $mh->create_tag($field->name, $field->tag);

        return $field;

    }

}