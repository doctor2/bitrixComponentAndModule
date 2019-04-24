<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option,
    \Sebekon\Helper\PortfolioSection,
	\Sebekon\Helper\User;

class CSebekonPortfolio extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['USER'] = User::getCurrentUserAndStudent();

        $this->generateUrlAliasesAndVariables();

        if ((int)$this->arResult['VARIABLES']['SECTION_ID'] <= 0)
        {
            LocalRedirect("/404.php", "404 Not Found");
        }

        if ($this->isAdmin($this->arResult['USER']))
        {
            $this->processAdmin();
        }
        elseif ($this->isStudent($this->arResult['USER']))
        {
            $this->processStudent();
        }
        else
        {
            LocalRedirect("/404.php", "404 Not Found");
        }
    }

    protected function generateUrlAliasesAndVariables()
    {
        $arComponentVariables = [
            'section',
            'element_id',
            'student_id',
            'action',
            'groups',
        ];

        $aliases = [
            'SECTION' => 'section',
            'ELEMENT_ID' => 'element_id',
            'STUDENT_ID' => 'student_id',
            'ACTION' => 'action',
            'GROUPS' => 'groups',
        ];

        \CComponentEngine::initComponentVariables(false, $arComponentVariables, $aliases, $arVariables);

        $dataSection = PortfolioSection::getInstance();

        $arVariables['CLASS'] = $dataSection->getSelectedClass();
        $arVariables['TEMPLATE'] = strtolower($arVariables['CLASS']);
        $arVariables['SECTION_ID'] = $dataSection->getSelectedSectionId();
        $arVariables['SECTION'] = $dataSection->getSelectedSectionCode();

        $this->arResult['ALIASES'] = $aliases;
        $this->arResult['VARIABLES'] = $arVariables;
    }

    protected function processAdmin()
    {
        $this->arResult['IS_ADMIN'] = 'Y';

        $aliases = $this->arResult['ALIASES'];
        $arVariables = $this->arResult['VARIABLES'];

        if ((int)$arVariables['STUDENT_ID'] > 0 && (int)$arVariables['ELEMENT_ID'] > 0)
        {
            $componentPage = 'admin_detail';
        }
        elseif ((int)$arVariables['STUDENT_ID'] > 0)
        {
            $componentPage = 'admin_list';
        }
        elseif ($arVariables['ACTION'] === 'file' && !empty($arVariables['GROUPS']))
        {
            $this->arResult['VARIABLES']['GROUPS'] = json_decode($arVariables['GROUPS']);

            $componentPage = 'admin_file';
        }
        else
        {
            $componentPage = 'admin';
        }

        $url = $GLOBALS['APPLICATION']->GetCurPage(false);

        $studentUrl = $url . "?" . $aliases["STUDENT_ID"] . "=" . $arVariables['STUDENT_ID'];

        $this->arResult["URL_TEMPLATES"] = [
            "admin" => htmlspecialcharsbx($url),
            "admin_list" => htmlspecialcharsbx($studentUrl . '&' . $aliases["SECTION"] . "=" . $arVariables['SECTION']),
            "admin_detail" => htmlspecialcharsbx($studentUrl . '&' . $aliases["SECTION"] . "=" . $arVariables['SECTION']
                . "&" . $aliases["ELEMENT_ID"] . '=#' . $aliases["ELEMENT_ID"] . '#'),
            "admin_file" => htmlspecialcharsbx($url . '?' . $aliases["ACTION"] . '=file'),
            "selector" => htmlspecialcharsbx($studentUrl),
        ];

        $this->IncludeComponentTemplate($componentPage);
    }

    protected function processStudent()
    {
        $aliases = $this->arResult['ALIASES'];
        $arVariables = $this->arResult['VARIABLES'];

        if ($arVariables['ACTION'] === 'file' && (int)$arVariables['ELEMENT_ID'] > 0)
        {
            $componentPage = 'file';
        }
        elseif ($arVariables['ACTION'] === 'form')
        {
            $componentPage = 'form';
        }
        else
        {
            $componentPage = 'list';
        }

        $url = $GLOBALS['APPLICATION']->GetCurPage(false);
        $sectionUrl = $url . '?' . $aliases["SECTION"] . '=' . $arVariables['SECTION'];

        $this->arResult["URL_TEMPLATES"] = [
            "selector" => htmlspecialcharsbx($url),
            "list" => htmlspecialcharsbx($sectionUrl),
            "form" => htmlspecialcharsbx($sectionUrl . '&' . $aliases["ACTION"] . '=form'),
            "element_form" => htmlspecialcharsbx($sectionUrl . '&' . $aliases["ACTION"] . '=form&'
                . $aliases["ELEMENT_ID"] . '=#' . $aliases["ELEMENT_ID"] . '#'),
            "file" => htmlspecialcharsbx($sectionUrl . '&' . $aliases["ACTION"] . '=file&'
                . $aliases["ELEMENT_ID"] . '=#' . $aliases["ELEMENT_ID"] . '#'),
            "ajax_deleting_element" => htmlspecialcharsbx('/ajax/portfolio/delete.php' . '?' . $aliases["SECTION"] . '=' . $arVariables['SECTION']),
            "ajax_adding_element" => htmlspecialcharsbx('/ajax/portfolio/add.php' . '?' . $aliases["SECTION"] . '=' . $arVariables['SECTION']),
            "ajax_deleting_file" => htmlspecialcharsbx('/ajax/portfolio/delete_file.php' . '?' . $aliases["SECTION"] . '=' . $arVariables['SECTION']),
        ];

        $this->IncludeComponentTemplate($componentPage);
    }

    protected function isAdmin($user)
    {
        $userGroups = CUser::GetUserGroupArray($user['ID']);
        $adminGroupId = Option::get(SEBEKON_CONSTANTS['SETTINGS_MODULE_ID'], 'portfolio_group_id');

        return in_array($adminGroupId, $userGroups);
    }

    protected function isStudent($user)
    {
        return !empty($user['STUDENT_GUID']);
    }

}


