<?php

namespace Deform\Form\Model;

interface IModel {
    public static function getTable(): string;
    public static function getPersistedFields(): array;
}
