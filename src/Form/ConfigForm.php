<?php
declare(strict_types=1);

namespace FileManager\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Override;

/**
 * Config Form.
 */
class ConfigForm extends Form
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('basePath', 'string');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function validationDefault(Validator $validator): Validator
    {
        return $validator->scalar('basePath')
                        ->allowEmptyString('basePath')
                        ->add('basePath', 'validFolderChars', [
                            'rule' => ['custom', '/^[A-Za-z0-9_\-\/\.]+$/'],
                            'message' => 'Only letters, numbers, dashes, underscores, slashes, and dots are allowed.',
                        ])
                        ->add('basePath', 'noLeadingTrailingSlash', [
                            'rule' => ['custom', '/^(?!\/)(?!.*\/$).*$/'],
                            'message' => 'No leading or trailing slashes are allowed.',
        ]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function process(array $data): bool
    {
        Configure::write($data);

        return Configure::dump('FileManager', 'db', array_keys($data));
    }
}
