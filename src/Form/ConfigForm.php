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
     * Builds the schema for the modelless form
     *
     * @param \Cake\Form\Schema $schema From schema
     * @return $this
     */
    #[Override]
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('basePath', 'string');
    }

    /**
     * Form validation builder
     *
     * @param \Cake\Validation\Validator $validator to use against the form
     * @return \Cake\Validation\Validator
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
     * Defines what to execute once the From is being processed
     *
     * @return bool
     */
    #[Override]
    protected function _execute(array $data): bool
    {
        Configure::write($data);

        return Configure::dump('FileManager', 'db', array_keys($data));
    }
}
