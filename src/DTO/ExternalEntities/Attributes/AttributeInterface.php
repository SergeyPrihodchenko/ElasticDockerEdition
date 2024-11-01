<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities\Attributes;

interface AttributeInterface
{
   // Тип в поисковых подсказках
   public function getType(): string;

   // Значение в подсказках
   public function getValue(): string;

   /**
    * Возвращает массив из полей и их значений, 
    * которые должны сохранятся и отдаваться из поискового индекс.
    * Должны быть геттеры и сеттеры на каждое из таких свойств
    *
    * @return array<string, mixed>
    */
   public function __serialize(): array;
   
}
